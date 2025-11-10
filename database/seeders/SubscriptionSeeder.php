<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('subscriptions')->insert([
            [
                'name' => 'Daily',
                'duration_days' => 1,
                'price' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly',
                'duration_days' => 30,
                'price' => 450,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yearly',
                'duration_days' => 365,
                'price' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
