<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(50000, 5000000),
            'stock' => fake()->numberBetween(0, 100),
            'image_path' => null,
        ];
    }
}