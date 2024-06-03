<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;

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
            'sku' => $this->faker->word(),
            'name' => $this->faker->name(),
            'purchase_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'selling_price' => $this->faker->randomFloat(0, 0, 9999999999.),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(["active","inactive"]),
        ];
    }
}
