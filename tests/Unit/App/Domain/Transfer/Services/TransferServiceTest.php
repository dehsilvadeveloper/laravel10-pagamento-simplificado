<?php

namespace Tests\Unit\App\Domain\Transfer\Services;

use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Events\TransferReceived;
use App\Domain\Transfer\Exceptions\TransferFailedException;
use App\Domain\Transfer\Exceptions\UnauthorizedTransferException;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Repositories\TransferRepositoryInterface;
use App\Domain\Transfer\Services\TransferService;
use App\Domain\Transfer\ValueObjects\TransferParamsObject;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Database\Seeders\TransferStatusSeeder;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    /** @var TransferService */
    private $service;

    /** @var MockInterface */
    private $transferAuthorizationServiceMock;

    /** @var MockInterface */
    private $transferRepositoryMock;

    /** @var MockInterface */
    private $walletRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TransferStatusSeeder::class);

        $this->transferAuthorizationServiceMock = Mockery::mock(TransferAuthorizerServiceInterface::class);
        $this->transferRepositoryMock = Mockery::mock(TransferRepositoryInterface::class);
        $this->walletRepositoryMock = Mockery::mock(WalletRepositoryInterface::class);
        $this->service = app(
            TransferService::class,
            [
                'transferAuthorizationService' => $this->transferAuthorizationServiceMock,
                'transferRepository' => $this->transferRepositoryMock,
                'walletRepository' => $this->walletRepositoryMock
            ]
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group services
     * @group transfer
     */
    public function test_can_transfer(): void
    {
        Event::fake();

        $amountToBeTransferred = 50.00;
        $authorizationDate = '2024-04-05 10:15:05';

        $payer = $this->generateFakePayer();
        $payee = $this->generateFakePayee();
        $transferParamsObjectMock = $this->generateTransferParamsObject($payer, $payee, $amountToBeTransferred);
        $transfer = $this->generateFakeTransfer($payer, $payee, $amountToBeTransferred);

        $this->transferAuthorizationServiceMock->shouldReceive('authorize')->once()->andReturn(true);

        $this->transferRepositoryMock->shouldReceive('create')->once()->andReturn($transfer);
        $this->transferRepositoryMock->shouldReceive('updateAuthorizationDate')
            ->once()
            ->andReturnUsing(function () use ($transfer, $authorizationDate) {
                $transfer->authorized_at = $authorizationDate;
                return $transfer;
            });
        $this->transferRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($transfer->id, TransferStatusEnum::COMPLETED)
            ->andReturnUsing(function () use ($transfer) {
                $transfer->transfer_status_id = TransferStatusEnum::COMPLETED->value;
                return $transfer;
            });

        $this->walletRepositoryMock->shouldReceive('decrementById')->once();
        $this->walletRepositoryMock->shouldReceive('incrementById')->once();

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();

        $result = $this->service->transfer($transferParamsObjectMock);

        Event::assertDispatched(TransferReceived::class);

        $this->assertInstanceOf(Transfer::class, $result);
        $this->assertEquals($payer->id, $result->payer_id);
        $this->assertEquals($payee->id, $result->payee_id);
        $this->assertEquals($amountToBeTransferred, $result->amount);
        $this->assertEquals(TransferStatusEnum::COMPLETED->value, $result->transfer_status_id);
        $this->assertEquals($authorizationDate, $result->authorized_at);
    }

    /**
     * @group services
     * @group transfer
     */
    public function test_cannot_transfer_if_authorizer_denies(): void
    {
        Event::fake();

        $this->expectException(UnauthorizedTransferException::class);

        $amountToBeTransferred = 50.00;

        $payer = $this->generateFakePayer();
        $payee = $this->generateFakePayee();
        $transferParamsObjectMock = $this->generateTransferParamsObject($payer, $payee, $amountToBeTransferred);
        $transfer = $this->generateFakeTransfer($payer, $payee, $amountToBeTransferred);

        $this->transferAuthorizationServiceMock->shouldReceive('authorize')->once()->andReturn(false);

        $this->transferRepositoryMock->shouldReceive('create')->once()->andReturn($transfer);
        $this->transferRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($transfer->id, TransferStatusEnum::UNAUTHORIZED)
            ->andReturnUsing(function () use ($transfer) {
                $transfer->transfer_status_id = TransferStatusEnum::UNAUTHORIZED->value;
                return $transfer;
            });

        $this->service->transfer($transferParamsObjectMock);
    }

    /**
     * @group services
     * @group transfer
     */
    public function test_undo_wallet_changes_if_exception_occurs(): void
    {
        Event::fake();

        $this->expectException(TransferFailedException::class);

        $amountToBeTransferred = 50.00;
        $authorizationDate = '2024-04-05 10:15:05';

        $payer = $this->generateFakePayer();
        $payee = $this->generateFakePayee();
        $transferParamsObjectMock = $this->generateTransferParamsObject($payer, $payee, $amountToBeTransferred);
        $transfer = $this->generateFakeTransfer($payer, $payee, $amountToBeTransferred);

        $this->transferAuthorizationServiceMock->shouldReceive('authorize')->once()->andReturn(true);

        $this->transferRepositoryMock->shouldReceive('create')->once()->andReturn($transfer);
        $this->transferRepositoryMock->shouldReceive('updateAuthorizationDate')
            ->once()
            ->andReturnUsing(function () use ($transfer, $authorizationDate) {
                $transfer->authorized_at = $authorizationDate;
                return $transfer;
            });
        $this->transferRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($transfer->id, TransferStatusEnum::ERROR)
            ->andReturnUsing(function () use ($transfer) {
                $transfer->transfer_status_id = TransferStatusEnum::ERROR->value;
                return $transfer;
            });

        $this->walletRepositoryMock->shouldReceive('decrementById')->once();
        $this->walletRepositoryMock->shouldReceive('incrementById')
            ->once()
            ->andThrow(new Exception('Fake Database error'));

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollback')->once();

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[TransferService] Failed to execute the transfer between the users as requested.'
                ) !== false
                    && strpos($context['error_message'], 'Fake Database error') !== false;
            });

        $this->service->transfer($transferParamsObjectMock);
    }

    private function generateFakePayer(): User
    {
        $payer = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payer->id = 5;

        $payerWallet = Wallet::factory()->for($payer)->make([
            'balance' => 400.00
        ]);
        $payerWallet->id = 20;

        $payer->setRelation('wallet', $payerWallet);

        return $payer;
    }

    private function generateFakePayee(): User
    {
        $payee = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee->id = 6;

        $payeeWallet = Wallet::factory()->for($payee)->make([
            'balance' => 100.00
        ]);
        $payeeWallet->id = 21;

        $payee->setRelation('wallet', $payeeWallet);

        return $payee;
    }

    private function generateTransferParamsObject(
        User $payer,
        User $payee,
        float $amountToBeTransferred
    ): MockInterface|TransferParamsObject {
        /** @var MockInterface|TransferParamsObject $transferParamsObjectMock */
        $transferParamsObjectMock = Mockery::mock(TransferParamsObject::class);
        $transferParamsObjectMock->shouldReceive('getPayerId')->andReturn($payer->id);
        $transferParamsObjectMock->shouldReceive('getPayeeId')->andReturn($payee->id);
        $transferParamsObjectMock->shouldReceive('getAmount')->andReturn($amountToBeTransferred);
        $transferParamsObjectMock->shouldReceive('toArray')->andReturn([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => $amountToBeTransferred
        ]);

        return $transferParamsObjectMock;
    }

    private function generateFakeTransfer(User $payer, User $payee, float $amountToBeTransferred): Transfer
    {
        $transfer = Transfer::factory()->make([
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => $amountToBeTransferred,
            'transfer_status_id' => TransferStatusEnum::PENDING->value
        ]);
        $transfer->id = 10;
        $transfer->setRelation('payer', $payer);
        $transfer->setRelation('payee', $payee);

        return $transfer;
    }
}
