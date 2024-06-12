<?php

namespace App\Repositories;

use App\Enums\SubscriptionStatusEnum;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRepository extends BaseRepository
{
    /**
     * @param  Subscription  $model
     */
    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model|null
     */
    public function getActiveSubscriptionByCompanyId(string $companyId): ?Model
    {
        return $this->model
            ->where('company_id', $companyId)
            ->where('status', SubscriptionStatusEnum::ACTIVE)
            ->first();
    }

    /**
     * @param  array  $data
     * @return Subscription
     */
    public function create(array $data): Subscription
    {
        return $this->model->create([
            'company_id' => $data['company_id'],
            'plan_id' => $data['plan_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'extra_users' => $data['extra_users'],
        ]);
    }
}
