<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Models\TransferStatus;
use App\Domain\User\Models\User;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = Carbon::now();

        return [
            'payer_id' => User::factory(),
            'payee_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 10, 900),
            'transfer_status_id' => TransferStatus::factory(),
            'created_at' => $now,
            'updated_at' => $now,
            'authorized_at' => null
        ];
    }
}
