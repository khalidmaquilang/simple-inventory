<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'user_id' => User::factory()->create()->id,
            'product_id' => Product::factory()->create()->id,
            'quantity_on_hand' => 10,
            'average_cost' => 1,
        ];
    }
}
