<?php

namespace Database\Factories;

use Arr;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name_categories = ['action', 'adventure', 'fiction', 'comedy', 'Drama', 'fantasy', 'suspense', 'terror'];
        $name = $this->faker->company . ' '. Arr::random($name_categories);

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'description' => $this->faker->realText(280),
            'price' => $this->faker->numberBetween('20000', '200000')
        ];
    }
}
