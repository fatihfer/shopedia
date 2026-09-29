<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    private const POOL = [
        'Sneakers',
        'Skincare',
        'Kopi',
        'Elektronik',
        'Fashion',
        'Rumah Tangga',
        'Aksesoris HP',
        'Olahraga',
        'Kesehatan',
        'Mainan Anak',
    ];

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(self::POOL);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
