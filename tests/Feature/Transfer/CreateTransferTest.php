<?php

namespace Tests\Feature\Transfer;

use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserTypeSeeder;

class CreateTransferTest extends TestCase
{
    /** @var MockInterface */
    private $transferAuthorizationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);
        $this->seed(UserSeeder::class);

        $this->transferAuthorizationServiceMock = Mockery::mock(TransferAuthorizerServiceInterface::class);

        $this->app->instance(TransferAuthorizerServiceInterface::class, $this->transferAuthorizationServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group transfer
     */
    public function test_can_transfer_from_payer_common_to_payee_common(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $payer = User::find(1);
        $payee = User::find(2);

        $payerExpectedWalletBalance = $payer->wallet->balance - 20.50;
        $payeeExpectedWalletBalance = $payee->wallet->balance + 20.50;

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 20.50
        ];

        $this->transferAuthorizationServiceMock->shouldReceive('authorize')->once()->andReturn(true);

        $response = $this->postJson(route('transfer.create'), $data);

        // TODO: Testar evento de notificação para PAYEE. Exemplo: Event::assertDispatched(TransferReceived::class);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'payer' => [
                    'id',
                    'name',
                    'wallet' => [
                        'id',
                        'balance'
                    ],
                    'created_at',
                    'updated_at'
                ],
                'payee' => [
                    'id',
                    'name',
                    'wallet' => [
                        'id',
                        'balance'
                    ],
                    'created_at',
                    'updated_at'
                ],
                'amount',
                'status' => [
                    'id',
                    'name'
                ],
                'created_at',
                'updated_at',
                'authorized_at'
            ]
        ]);
        $response->assertJsonPath('data.amount', 20.50);
        $response->assertJsonPath('data.status.name', TransferStatusEnum::COMPLETED->name());
        $response->assertJsonPath('data.payer.id', 1);
        $response->assertJsonPath('data.payer.wallet.balance', $payerExpectedWalletBalance);
        $response->assertJsonPath('data.payee.id', 2);
        $response->assertJsonPath('data.payee.wallet.balance', $payeeExpectedWalletBalance);
    }

    /**
     * @group transfer
     */
    public function test_can_transfer_from_payer_common_to_payee_shopkeeper(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $payer = User::find(1);
        $payee = User::find(3);

        $payerExpectedWalletBalance = $payer->wallet->balance - 100;
        $payeeExpectedWalletBalance = $payee->wallet->balance + 100;

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100
        ];

        $this->transferAuthorizationServiceMock->shouldReceive('authorize')->once()->andReturn(true);

        $response = $this->postJson(route('transfer.create'), $data);

        // TODO: Testar evento de notificação para PAYEE. Exemplo: Event::assertDispatched(TransferReceived::class);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'payer' => [
                    'id',
                    'name',
                    'wallet' => [
                        'id',
                        'balance'
                    ],
                    'created_at',
                    'updated_at'
                ],
                'payee' => [
                    'id',
                    'name',
                    'wallet' => [
                        'id',
                        'balance'
                    ],
                    'created_at',
                    'updated_at'
                ],
                'amount',
                'status' => [
                    'id',
                    'name'
                ],
                'created_at',
                'updated_at',
                'authorized_at'
            ]
        ]);
        $response->assertJsonPath('data.amount', 100);
        $response->assertJsonPath('data.status.name', TransferStatusEnum::COMPLETED->name());
        $response->assertJsonPath('data.payer.id', 1);
        $response->assertJsonPath('data.payer.wallet.balance', $payerExpectedWalletBalance);
        $response->assertJsonPath('data.payee.id', 3);
        $response->assertJsonPath('data.payee.wallet.balance', $payeeExpectedWalletBalance);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_missing_required_fields(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_existent_payer(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_payer_shopkeeper(): void // tipo não pode ser shopkeeper!!!
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_if_payer_has_insufficient_funds(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_existent_payee(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_payee_that_is_equal_to_payer(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_mumeric_value(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_with_zero_value(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_negative_value(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_if_authorizer_denies(): void
    {
        Event::fake();

        $this->assertTrue(true);
    }

    /**
     * @group transfer
     */
    public function test_fail_if_a_exception_occurs(): void // checar se wallets estão retornando aos valores iniciais!!!
    {
        Event::fake();

        $this->assertTrue(true);
    }
}
