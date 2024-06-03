<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->word(),
            'contact_person' => $this->faker->word(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
