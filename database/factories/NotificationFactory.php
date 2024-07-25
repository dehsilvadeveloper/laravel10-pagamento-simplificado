<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domain\Notification\Models\Notification;
use App\Domain\User\Models\User;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = Carbon::now();

        return [
            'recipient_id' => User::factory()->create(),
            'type' => 'App\\Domain\\User\\Notifications\\GenericNotification',
            'channel' => fake()->randomElement(['mail', 'sms']),
            'response' => 'Notification sent successfully.',
            'created_at' => $now,
            'updated_at' => $now
        ];
    }
}
