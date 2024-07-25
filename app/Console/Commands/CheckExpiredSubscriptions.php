<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatusEnum;
use App\Events\PrepareFreemium;
use App\Jobs\SendSubscriptionExpiredJob;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command checks for active subscriptions that have expired based on their `end_date`. Expired subscriptions are then marked as 'expired'.";

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $subscriptions = $subscriptionService->getExpiredSubscriptions();
        foreach ($subscriptions as $subscription) {
            $subscription->status = SubscriptionStatusEnum::PAST_DUE;
            $subscription->save();

            event(new PrepareFreemium($subscription->company));

            SendSubscriptionExpiredJob::dispatch($subscription->company)
                ->onQueue('short-running-queue');
        }
    }
}
