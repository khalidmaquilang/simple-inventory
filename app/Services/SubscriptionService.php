<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * @param  SubscriptionRepository  $subscriptionRepository
     */
    public function __construct(protected SubscriptionRepository $subscriptionRepository)
    {
    }

    /**
     * @param  array  $data
     * @return Subscription|null
     */
    public function store(array $data): ?Subscription
    {
        try {
            return $this->subscriptionRepository->create([
                'company_id' => $data['company_id'],
                'plan_id' => $data['plan_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
                'extra_users' => $data['extra_users'] ?? 0,
            ]);
        } catch (\Exception $exception) {
            Log::error('There was something wrong while storing subscription.', [
                'data' => $data,
                'user_id' => auth()->id(),
                'company_id' => filament()->getTenant()->id,
            ]);

            abort(500, $exception->getMessage());
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getFreemiumSubscription()
    {
        return $this->subscriptionRepository->getFreemiumSubscription();
    }
}
