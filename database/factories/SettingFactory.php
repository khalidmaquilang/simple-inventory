<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->word(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->text(),
            'currency' => $this->faker->word(),
        ];
    }
}
