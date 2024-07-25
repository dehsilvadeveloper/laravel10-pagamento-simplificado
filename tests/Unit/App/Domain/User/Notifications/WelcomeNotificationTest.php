<?php

namespace Tests\Unit\App\Domain\User\Notifications;

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Domain\User\Models\User;
use App\Domain\User\Notifications\WelcomeNotification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class WelcomeNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group notifications
     * @group user
     */
    public function test_is_sent_by_mail(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->notify(new WelcomeNotification());

        Notification::assertSentTo($user, WelcomeNotification::class, function ($notification, $channels) {
            return in_array('mail', $channels);
        });
    }

    /**
     * @group notifications
     * @group user
     */
    public function test_is_sent_by_sms(): void
    {
        Notification::fake();

        $user = User::factory()->make();

        $user->notify(new WelcomeNotification());

        Notification::assertSentTo($user, WelcomeNotification::class, function ($notification, $channels) {
            return in_array('sms', $channels);
        });
    }
}
