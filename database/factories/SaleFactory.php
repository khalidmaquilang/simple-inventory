<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'sale_date' => $this->faker->date(),
            'vat' => $this->faker->randomFloat(0, 0, 9999999999.),
            'total_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'paid_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'customer_id' => Customer::factory(),
            'payment_type_id' => PaymentType::factory(),
            'user_id' => User::factory(),
            'discount' => $this->faker->randomFloat(0, 0, 1000.),
            'discount_type' => 'fixed',
            'pay_until' => 1,
        ];
    }
}
