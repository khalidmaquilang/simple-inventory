<?php

namespace App\Services;

use App\Repositories\PlanRepository;

class PlanService
{
    /**
     * @param  PlanRepository  $planRepository
     */
    public function __construct(protected PlanRepository $planRepository)
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getFreemiumPlan()
    {
        return $this->planRepository->getFreemiumPlan();
    }
}
