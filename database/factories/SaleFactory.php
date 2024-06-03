<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\PaymentType;
use App\Models\Sale;

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
            'invoice_number' => $this->faker->word(),
            'sale_date' => $this->faker->date(),
            'vat' => $this->faker->randomFloat(0, 0, 9999999999.),
            'total_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'paid_amount' => $this->faker->randomFloat(0, 0, 9999999999.),
            'customer_id' => Customer::factory(),
            'payment_type_id' => PaymentType::factory(),
        ];
    }
}
