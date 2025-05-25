<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\LoaningForm;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemScanApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a location for items
        $this->location = Location::factory()->create([
            'name' => 'Test Location'
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_scan_endpoint()
    {
        $item = Item::factory()->create();

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401);
    }

    #[Test]
    public function invalid_item_id_returns_validation_error()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/scan', ['id' => 999999]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['id']);
    }

    #[Test]
    public function missing_id_parameter_returns_validation_error()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/scan', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['id']);
    }

    #[Test]
    public function manager_can_scan_normal_status_item()
    {
        $manager = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'normal'
        ]);

        Sanctum::actingAs($manager);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Scan successful']);
    }

    #[Test]
    public function non_manager_cannot_scan_normal_status_item()
    {
        $manager = User::factory()->create();
        $nonManager = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'normal'
        ]);

        Sanctum::actingAs($nonManager);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
    }

    #[Test]
    public function manager_can_scan_registered_status_item_and_updates_to_normal()
    {
        $manager = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'registered'
        ]);

        Sanctum::actingAs($manager);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item status updated to normal']);

        $this->assertEquals('normal', $item->fresh()->status);
    }

    #[Test]
    public function non_manager_cannot_scan_registered_status_item()
    {
        $manager = User::factory()->create();
        $nonManager = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'registered'
        ]);

        Sanctum::actingAs($nonManager);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
        
        // Status should remain unchanged
        $this->assertEquals('registered', $item->fresh()->status);
    }

    #[Test]
    public function user_can_scan_reserved_item_to_start_loan()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        $loaningForm = LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'approved',
            'start_at' => null,
            'end_at' => null
        ]);

        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item loaned successfully']);

        $loaningForm->refresh();
        $item->refresh();

        $this->assertNotNull($loaningForm->start_at);
        $this->assertNull($loaningForm->end_at);
        $this->assertEquals($applicant->id, $item->owner_id);
    }

    #[Test]
    public function user_can_scan_reserved_item_to_return_loan()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $applicant->id, // Currently owned by applicant
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        $loaningForm = LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'approved',
            'start_at' => now()->subDays(1), // Already started
            'end_at' => null
        ]);

        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item returned successfully']);

        $loaningForm->refresh();
        $item->refresh();

        $this->assertNotNull($loaningForm->start_at);
        $this->assertNotNull($loaningForm->end_at);
        $this->assertEquals($manager->id, $item->owner_id); // Owner back to manager
    }

    #[Test]
    public function user_cannot_scan_reserved_item_without_approved_loaning_form()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        // No loaning form exists
        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
    }

    #[Test]
    public function user_cannot_scan_reserved_item_with_only_requested_loaning_form()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'requested', // Not approved
            'start_at' => null,
            'end_at' => null
        ]);

        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
    }

    #[Test]
    public function user_cannot_scan_reserved_item_with_completed_loan()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'approved',
            'start_at' => now()->subDays(2), // Already started
            'end_at' => now()->subDays(1)    // Already returned
        ]);

        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
    }

    #[Test]
    public function scanning_gone_status_item_returns_unauthorized()
    {
        $manager = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'gone'
        ]);

        Sanctum::actingAs($manager);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized scan']);
    }

    #[Test]
    public function multiple_approved_loaning_forms_uses_latest_one()
    {
        $manager = User::factory()->create();
        $applicant = User::factory()->create();
        $item = Item::factory()->create([
            'manager_id' => $manager->id,
            'owner_id' => $manager->id,
            'location_id' => $this->location->id,
            'status' => 'reserved'
        ]);

        // Create an old completed loaning form
        LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'approved',
            'start_at' => now()->subDays(10),
            'end_at' => now()->subDays(5),
            'created_at' => now()->subDays(10)
        ]);

        // Create a newer approved loaning form
        $latestForm = LoaningForm::factory()->create([
            'item_id' => $item->id,
            'applicant_id' => $applicant->id,
            'status' => 'approved',
            'start_at' => null,
            'end_at' => null,
            'created_at' => now()->subDays(1)
        ]);

        Sanctum::actingAs($applicant);

        $response = $this->postJson('/api/scan', ['id' => $item->id]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Item loaned successfully']);

        $latestForm->refresh();
        $this->assertNotNull($latestForm->start_at);
    }
}
