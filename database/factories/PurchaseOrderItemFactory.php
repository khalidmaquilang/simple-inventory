<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class PurchaseOrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseOrderItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->word(),
            'name' => $this->faker->name(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'unit_cost' => $this->faker->randomFloat(0, 0, 9999999999.),
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
