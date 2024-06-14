<?php

namespace Database\Factories;

use App\Enums\BillingCycleEnum;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'price' => 0,
            'billing_cycle' => BillingCycleEnum::MONTHLY,
            'max_users' => 1,
            'max_roles' => 1,
            'max_monthly_purchase_order' => 10,
            'max_monthly_sale_order' => 10,
            'type' => $this->faker->randomElement(['standard', 'custom']),
        ];
    }
}
