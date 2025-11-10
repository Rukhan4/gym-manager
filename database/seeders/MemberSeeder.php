<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('members')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subscription_id' => 1, // Daily
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarah Smith',
                'email' => 'sarah@example.com',
                'subscription_id' => 2, // Monthly
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Michael Green',
                'email' => 'michael@example.com',
                'subscription_id' => 3, // Yearly
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
