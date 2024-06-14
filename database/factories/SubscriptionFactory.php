<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory()->create()->id,
            'plan_id' => Plan::factory()->create()->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'status' => $this->faker->randomElement(['active', 'trialing', 'canceled', 'past_due', 'unpaid']),
            'extra_users' => 0,
            'total_amount' => 0,
        ];
    }
}
