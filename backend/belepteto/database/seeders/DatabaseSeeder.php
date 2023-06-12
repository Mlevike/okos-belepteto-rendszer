<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Kezdeti beállítások megadása
        DB::table('settings')->insert([
            'setting_name' => 'access_token',
            'setting_value' => ''
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'isEntryEnabled',
            'setting_value' =>  true
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'isExitEnabled',
            'setting_value' => true
        ]);
    }
}
