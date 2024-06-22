<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'category_id' => Category::factory()->create()->id,
            'sku' => $this->faker->word(),
            'name' => $this->faker->name(),
            'purchase_price' => $this->faker->randomFloat(0, 0, 1000.),
            'selling_price' => $this->faker->randomFloat(0, 0, 1000.),
            'reorder_point' => 0,
            'last_notified_at' => now(),
            'description' => $this->faker->text(),
        ];
    }
}
