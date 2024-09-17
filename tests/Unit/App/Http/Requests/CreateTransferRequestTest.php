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
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => fake()->randomFloat(2, 10, 900)
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
            'payer' => 999,
            'payee' => $payee->id,
            'value' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payer'));
        $this->assertEquals(
            [
                'The selected payer is invalid.'
            ],
            $validator->errors()->get('payer')
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
            'payer' => $payer->id,
            'payee' => 999,
            'value' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payee'));
        $this->assertEquals(
            [
                'The selected payee is invalid.'
            ],
            $validator->errors()->get('payee')
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
            'payer' => $payer->id,
            'payee' => $payer->id,
            'value' => fake()->randomFloat(2, 10, 900)
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('payee'));
        $this->assertEquals(
            [
                'The payee field and payer must be different.'
            ],
            $validator->errors()->get('payee')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_non_mumeric_value(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 'abc'
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(2, $validator->errors());
        $this->assertTrue($validator->errors()->has('value'));
        $this->assertEquals(
            [
                'The value field must be a number.',
                'The value field must be greater than 0.'
            ],
            $validator->errors()->get('value')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_with_zero_value(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 0
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('value'));
        $this->assertEquals(
            [
                'The value field must be greater than 0.'
            ],
            $validator->errors()->get('value')
        );
    }

    /**
     * @group requests
     * @group transfer
     */
    public function test_fail_with_value_with_negative_value(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => -20
        ];

        $request = (new CreateTransferRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('value'));
        $this->assertEquals(
            [
                'The value field must be greater than 0.'
            ],
            $validator->errors()->get('value')
        );
    }
}
