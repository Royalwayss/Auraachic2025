<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to the SQL file
        $path = database_path('seeders/countries.sql');

        // Read the SQL file
        $sql = file_get_contents($path);

        // Execute the SQL
        DB::unprepared($sql);

        $this->command->info('Database seeded from countries.sql');
    }
}
