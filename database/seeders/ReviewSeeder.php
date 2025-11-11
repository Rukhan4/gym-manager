<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Create 10 sample reviews
        for ($i = 0; $i < 3; $i++) {
            Review::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'rating' => $faker->numberBetween(1, 5),
                'comment' => $faker->paragraphs(2, true),
            ]);
        }
    }
}
