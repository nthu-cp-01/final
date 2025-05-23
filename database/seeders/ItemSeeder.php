<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test user and location
        $testUser = User::where('email', 'test@test.com')->first();
        $testLocation = Location::where('name', 'Testing Location in R744')->first();
        
        if (!$testUser || !$testLocation) {
            return;
        }
        
        // Create test items
        Item::factory()->create([
            'name' => 'Laptop Dell XPS',
            'purchase_date' => now()->subMonths(6),
            'description' => 'High-end laptop for development',
            'manager_id' => $testUser->id,
            'location_id' => $testLocation->id,
            'owner_id' => $testUser->id,
            'status' => 'normal',
        ]);
        
        Item::factory()->create([
            'name' => 'Monitor LG Ultrawide',
            'purchase_date' => now()->subMonths(3),
            'description' => '34-inch curved monitor',
            'manager_id' => $testUser->id,
            'location_id' => $testLocation->id,
            'owner_id' => $testUser->id,
            'status' => 'registered',
        ]);
        
        Item::factory()->create([
            'name' => 'Office Chair Ergonomic',
            'purchase_date' => now()->subMonths(12),
            'description' => 'Adjustable office chair with lumbar support',
            'manager_id' => $testUser->id,
            'location_id' => $testLocation->id,
            'owner_id' => $testUser->id,
            'status' => 'normal',
        ]);
    }
}
