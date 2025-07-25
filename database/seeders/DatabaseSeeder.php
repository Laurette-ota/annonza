<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the CurrencySeeder to populate the currencies table
        // This should be done before any other seeders that depend on currencies
        // For example, if you have a seeder for products that uses currency codes
        // it should be called after this seeder to ensure the currencies exist.

        $this->call([
            CurrencySeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
