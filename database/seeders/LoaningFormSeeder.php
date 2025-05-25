<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\LoaningForm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoaningFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test user
        $testUser = User::where('email', 'test@test.com')->first();
        
        if (!$testUser) {
            return;
        }
        
        // Create additional test users as applicants
        $applicant1 = User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@test.com',
            'password' => bcrypt('aoeuaoeu')
        ]);
        
        $applicant2 = User::factory()->create([
            'name' => 'Bob Smith', 
            'email' => 'bob@test.com',
            'password' => bcrypt('aoeuaoeu')
        ]);
        
        $applicant3 = User::factory()->create([
            'name' => 'Carol Davis',
            'email' => 'carol@test.com', 
            'password' => bcrypt('aoeuaoeu')
        ]);
        
        // Get existing items
        $laptopItem = Item::where('name', 'Laptop Dell XPS')->first();
        $monitorItem = Item::where('name', 'Monitor LG Ultrawide')->first();
        $chairItem = Item::where('name', 'Office Chair Ergonomic')->first();
        
        if (!$laptopItem || !$monitorItem || !$chairItem) {
            return;
        }
        
        // Create loaning forms with different statuses
        
        // 1. Requested loaning form for laptop
        LoaningForm::create([
            'item_id' => $laptopItem->id,
            'applicant_id' => $applicant1->id,
            'status' => 'requested',
            'start_at' => null,
            'end_at' => null,
        ]);
        
        // 2. Approved loaning form for monitor
        $approvedForm = LoaningForm::create([
            'item_id' => $monitorItem->id,
            'applicant_id' => $applicant2->id,
            'status' => 'approved',
        ]);

        // 3. Rejected loaning form for chair
        LoaningForm::create([
            'item_id' => $chairItem->id,
            'applicant_id' => $applicant3->id,
            'status' => 'rejected',
            'start_at' => null,
            'end_at' => null,
        ]);
        
        // 4. Another requested form from different applicant for chair
        LoaningForm::create([
            'item_id' => $chairItem->id,
            'applicant_id' => $applicant1->id,
            'status' => 'requested',
            'start_at' => null,
            'end_at' => null,
        ]);
        
        // 5. Old approved form (already completed)
        LoaningForm::create([
            'item_id' => $laptopItem->id,
            'applicant_id' => $applicant3->id,
            'status' => 'approved',
            'start_at' => now()->subDays(30),
            'end_at' => now()->subDays(5),
            'created_at' => now()->subDays(35),
            'updated_at' => now()->subDays(5),
        ]);
    }
}
