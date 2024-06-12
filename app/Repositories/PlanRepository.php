<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;

class PlanRepository extends BaseRepository
{
    /**
     * @param  Plan  $model
     */
    public function __construct(Plan $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model|null
     */
    public function getFreemiumPlan(): ?Model
    {
        return $this->model->find(1);
    }
}
