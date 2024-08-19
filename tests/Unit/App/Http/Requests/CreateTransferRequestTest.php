<?php

namespace Tests\Unit\App\Http\Requests;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CreateTransferRequest;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class CreateTransferRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_pass_with_valid_request(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_missing_required_fields(): void
    {
        $data = [];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(3, $validator->errors());
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_non_existent_payer(): void
    {
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => 999,
            'payee_id' => $payee->id,
            'amount' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payer_id'));
        $this->assertEquals(
            [
                'The selected payer id is invalid.'
            ],
            $validator->errors()->get('payer_id')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_non_existent_payee(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => 999,
            'amount' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payee_id'));
        $this->assertEquals(
            [
                'The selected payee id is invalid.'
            ],
            $validator->errors()->get('payee_id')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_payee_that_is_equal_to_payer(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payer->id,
            'amount' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payee_id'));
        $this->assertEquals(
            [
                'The payee id field and payer id must be different.'
            ],
            $validator->errors()->get('payee_id')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_non_mumeric_amount(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 'abc'
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(2, $validator->errors());
        $this->assertTrue($validator->errors()->has('amount'));
        $this->assertEquals(
            [
                'The amount field must be a number.',
                'The amount field must be greater than 0.'
            ],
            $validator->errors()->get('amount')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_amount_with_zero_value(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 0
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('amount'));
        $this->assertEquals(
            [
                'The amount field must be greater than 0.'
            ],
            $validator->errors()->get('amount')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_amount_with_negative_value(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => -20
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('amount'));
        $this->assertEquals(
            [
                'The amount field must be greater than 0.'
            ],
            $validator->errors()->get('amount')
        );
    }
}
