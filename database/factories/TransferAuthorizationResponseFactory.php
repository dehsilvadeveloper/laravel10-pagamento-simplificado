<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;

class TransferAuthorizationResponseFactory extends Factory
{
    protected $model = TransferAuthorizationResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transfer_id' => Transfer::factory(),
            'response' => fake()->randomFloat(2, 10, 900)
        ];
    }
}
