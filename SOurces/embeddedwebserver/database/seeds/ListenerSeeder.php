<?php

use Illuminate\Database\Seeder;
use App\Listener;

class ListenerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Listener::create([
            'listener_id' => "0000",
            'pin' => "0000"
        ]);
    }
}
