<?php

namespace App\Listeners;

use App\Enums\SubscriptionStatusEnum;
use App\Events\PrepareFreemium;
use App\Services\PlanService;
use App\Services\SubscriptionService;

class CreateFreemiumSubscription
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PrepareFreemium $event): void
    {
        $company = $event->company;
        $planService = app(PlanService::class);
        $subscriptionService = app(SubscriptionService::class);

        $plan = $planService->getFreemiumPlan();
        $subscriptionService->store([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'status' => SubscriptionStatusEnum::ACTIVE,
            'total_amount' => $plan->price,
        ]);
    }
}
