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
        DB::table('settings')->insert([
            'access_token' => '',
            'isEntryEnabled' => true,
            'isExitEnabled' => true
        ]);
    }
}
