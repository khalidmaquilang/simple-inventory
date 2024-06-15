<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\GoodsIssue;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodsIssueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoodsIssue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'sale_id' => Sale::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'supplier_id' => Supplier::factory()->create()->id,
            'issue_date' => now(),
            'sku' => $this->faker->word(),
            'name' => $this->faker->name(),
            'quantity' => $this->faker->numberBetween(-10000, 10000),
            'product_id' => Product::factory()->create()->id,
            'type' => $this->faker->randomElement(['sale', 'transfer', 'write_off', 'return_to_supplier']),
            'notes' => $this->faker->text,
        ];
    }
}
