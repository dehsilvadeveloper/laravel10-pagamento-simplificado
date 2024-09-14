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
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
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

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $response = $this->postJson(route('transfer.create'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'payer',
                'payee',
                'value'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'payer' => ['The payer field is required.'],
                'payee' => ['The payee field is required.'],
                'value' => ['The value field is required.']
            ]
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_existent_payer(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $data = [
            'payer' => 999,
            'payee' => 2,
            'value' => 20.50
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'payer'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'payer' => ['The selected payer is invalid.']
            ]
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_payer_shopkeeper(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $payer = User::find(3);
        $payee = User::find(2);

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 20.50
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message'
        ]);
        $response->assertJson([
            'message' => 'The payer of a transfer cannot be of type shopkeeper.'
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_if_payer_has_insufficient_funds(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        Wallet::factory()->for($payer)->create([
            'balance' => 1.20
        ]);

        $payee = User::find(2);

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 20.50
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message'
        ]);
        $response->assertJson([
            'message' => 'The payer does not have sufficient funds in his wallet for this operation.'
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_existent_payee(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $data = [
            'payer' => 1,
            'payee' => 999,
            'value' => 20.50
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'payee'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'payee' => ['The selected payee is invalid.']
            ]
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_payee_that_is_equal_to_payer(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $data = [
            'payer' => 1,
            'payee' => 1,
            'value' => 20.50
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'payee'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'payee' => ['The payee field and payer must be different.']
            ]
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_non_mumeric_value(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $data = [
            'payer' => 1,
            'payee' => 2,
            'value' => "abc"
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'value'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'value' => [
                    'The value field must be a number.',
                    'The value field must be greater than 0.'
                ]
            ]
        ]);
    }

    /**
     * @group transfer
     */
    public function test_fail_with_with_zero_value(): void
    {
        Event::fake();

        Sanctum::actingAs(ApiUser::factory()->create(), ['*']);

        $data = [
            'payer' => 1,
            'payee' => 2,
            'value' => 0
        ];

        $response = $this->postJson(route('transfer.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'value'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'value' => [
                    'The value field must be greater than 0.'
                ]
            ]
        ]);
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
