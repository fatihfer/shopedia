<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@shopedia.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'customer@shopedia.test'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        User::factory(8)->create(['role' => 'customer']);

        $categories = Category::factory(5)->create();

        Product::factory(30)->recycle($categories)->create();
    }
}
