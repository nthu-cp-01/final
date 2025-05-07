<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 sample locations
        Location::factory()->create([
            'name' => 'Testing Location in R744',
            'description' => 'This is a test location for R744',
            'deviceId' => 'named_test',
            'controllerShadowName' => 'controller',
            'sensorShadowName' => 'dht22',
        ]);
    }
}
