<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Transfer\Models\TransferStatus;

class TransferStatusFactory extends Factory
{
    protected $model = TransferStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word()
        ];
    }
}
