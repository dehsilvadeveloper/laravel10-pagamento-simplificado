<?php

namespace Tests\Unit\App\Domain\Transfer\ValueObjects;

use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use App\Domain\Transfer\Exceptions\InsufficientFundsException;
use App\Domain\Transfer\Exceptions\InvalidPayerException;
use App\Domain\Transfer\Exceptions\PayerNotFoundException;
use App\Domain\Transfer\ValueObjects\TransferParamsObject;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Wallet\Models\Wallet;

class TransferParamsObjectTest extends TestCase
{
    /** @var MockInterface */
    private $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);

        $this->app->instance(UserRepositoryInterface::class, $this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group value_objects
     * @group transfer
     */
    public function test_has_valid_params(): void
    {
        $payerRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payerRecord->id = 1;

        $walletRecord = Wallet::factory()->for($payerRecord)->make([
            'balance' => 700.50
        ]);

        $payerRecord->setRelation('wallet', $walletRecord);

        $this->userRepositoryMock->shouldReceive('firstById')->andReturn($payerRecord);

        $transferParamsObject = new TransferParamsObject(payerId: 1, payeeId: 2, amount: 100.00);
        $transferParamsAsArray = $transferParamsObject->toArray();

        $this->assertEquals(1, $transferParamsObject->getPayerId());
        $this->assertEquals(2, $transferParamsObject->getPayeeId());
        $this->assertEquals(100.00, $transferParamsObject->getAmount());
        $this->assertInstanceOf(User::class, $transferParamsObject->getPayer());
        $this->assertSame($payerRecord, $transferParamsObject->getPayer());
        $this->assertEquals(1, $transferParamsAsArray['payer_id']);
        $this->assertEquals(2, $transferParamsAsArray['payee_id']);
        $this->assertEquals(100.00, $transferParamsAsArray['amount']);
    }

    /**
     * @group value_objects
     * @group transfer
     */
    public function test_throw_exception_if_payer_not_found(): void
    {
        $this->expectException(PayerNotFoundException::class);

        $this->userRepositoryMock->shouldReceive('firstById')->andReturn(null);

        new TransferParamsObject(payerId: 1, payeeId: 2, amount: 100.00);
    }

    /**
     * @group value_objects
     * @group transfer
     */
    public function test_throw_exception_if_payer_type_is_invalid(): void
    {
        $this->expectException(InvalidPayerException::class);

        $payerRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::SHOPKEEPER->value
        ]);
        $payerRecord->id = 1;

        $this->userRepositoryMock->shouldReceive('firstById')->andReturn($payerRecord);

        new TransferParamsObject(payerId: 1, payeeId: 2, amount: 100.00);
    }

    /**
     * @group value_objects
     * @group transfer
     */
    public function test_throw_exception_if_payer_has_insufficient_funds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $payerRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payerRecord->id = 1;

        $walletRecord = Wallet::factory()->for($payerRecord)->make([
            'balance' => 20.50
        ]);
        
        $payerRecord->setRelation('wallet', $walletRecord);

        $this->userRepositoryMock->shouldReceive('firstById')->andReturn($payerRecord);

        new TransferParamsObject(payerId:1, payeeId:2, amount: 100.00);
    }
}
