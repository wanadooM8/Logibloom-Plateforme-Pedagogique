<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfesseurSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Professeur::factory()->count(10)->create();
    }
}
