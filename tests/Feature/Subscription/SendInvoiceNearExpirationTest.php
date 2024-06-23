<?php

namespace Tests\Feature\Subscription;

use App\Enums\PaymentStatusEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Plan;
use App\Models\Subscription;
use App\Notifications\InvoiceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendInvoiceNearExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_invoice_notification_one_week_before_expiration(): void
    {
        Notification::fake();

        /**
         * Near Expiration
         */
        $plan = Plan::factory()->create([
            'price' => 100,
        ]);
        $subscription = Subscription::factory()->create([
            'plan_id' => $plan->id,
            'total_amount' => $plan->price,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addWeek(),
        ]);

        $this->artisan('app:send-invoice-near-expired-subscriptions')->assertExitCode(0);

        Notification::assertSentTo([$subscription->company->owner], InvoiceNotification::class);
    }

    public function test_can_create_new_payment_for_invoice_notification_one_week_before_expiration(): void
    {
        /**
         * Near Expiration
         */
        $plan = Plan::factory()->create([
            'price' => 100,
        ]);
        $subscription = Subscription::factory()->create([
            'plan_id' => $plan->id,
            'total_amount' => $plan->price,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addWeek(),
        ]);

        $this->artisan('app:send-invoice-near-expired-subscriptions')->assertExitCode(0);

        $this->assertDatabaseHas('payments', [
            'company_id' => $subscription->company_id,
            'amount' => $subscription->total_amount,
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }

    public function test_cant_send_invoice_notification_less_than_a_week_before_expiration(): void
    {
        Notification::fake();

        /**
         * Near Expiration
         */
        $plan = Plan::factory()->create([
            'price' => 100,
        ]);
        Subscription::factory()->create([
            'plan_id' => $plan->id,
            'total_amount' => $plan->price,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addWeeks(2),
        ]);

        $this->artisan('app:send-invoice-near-expired-subscriptions')->assertExitCode(0);

        Notification::assertNothingSent();
    }
}
