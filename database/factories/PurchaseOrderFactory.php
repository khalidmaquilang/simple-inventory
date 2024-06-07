<?php

namespace Database\Factories;

use App\Models\PaymentType;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'purchase_code' => $this->faker->word(),
            'order_date' => $this->faker->date(),
            'expected_delivery_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'received', 'partially_received', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(0, 0, 100000),
            'paid_amount' => $this->faker->randomFloat(0, 0, 100000),
            'supplier_id' => Supplier::factory(),
            'payment_type_id' => PaymentType::factory(),
            'user_id' => User::factory(),
        ];
    }
}
