<?php

namespace Tests\Feature\Subscription;

use App\Enums\SubscriptionStatusEnum;
use App\Events\PrepareFreemium;
use App\Models\Subscription;
use App\Notifications\ExpiredNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CheckExpiredSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_change_status_to_past_due_expired_subscriptions(): void
    {
        /**
         * Already Expired
         */
        $subscription = Subscription::factory()->create([
            'status' => SubscriptionStatusEnum::ACTIVE,
            'end_date' => now()->subDay(),
        ]);

        $this->artisan('app:check-expired-subscriptions')->assertExitCode(0);

        $this->assertEquals($subscription->fresh()->status, SubscriptionStatusEnum::PAST_DUE);
    }

    public function test_can_fire_create_freemium_event_when_subscription_is_expired(): void
    {
        Event::fake();

        /**
         * Already Expired
         */
        Subscription::factory()->create([
            'status' => SubscriptionStatusEnum::ACTIVE,
            'end_date' => now()->subDay(),
        ]);

        $this->artisan('app:check-expired-subscriptions')->assertExitCode(0);
        Event::assertDispatched(PrepareFreemium::class);
    }

    public function test_can_send_notification_when_subscription_is_expired(): void
    {
        Notification::fake();

        /**
         * Already Expired
         */
        $subscription = Subscription::factory()->create([
            'status' => SubscriptionStatusEnum::ACTIVE,
            'end_date' => now()->subDay(),
        ]);

        $this->artisan('app:check-expired-subscriptions')->assertExitCode(0);

        Notification::assertSentTo(
            $subscription->company->owner,
            ExpiredNotification::class
        );
    }
}
