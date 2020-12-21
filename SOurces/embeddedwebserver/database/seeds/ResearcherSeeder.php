<?php

use Illuminate\Database\Seeder;
use App\Researcher;

class ResearcherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Researcher::create([
            'researcher_id' => "0000"
        ]);
    }
}
