<?php

namespace Tests\Feature;

use App\Jobs\ProcessItemsImport;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_page_can_be_accessed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/items-import');

        $response->assertStatus(200);
        /* $response->assertInertia(fn ($page) => $page->component('items/Import')); */
    }

    public function test_csv_file_can_be_uploaded()
    {
        Queue::fake();
        Storage::fake('local');

        $user = User::factory()->create();
        $location = Location::factory()->create(['name' => 'Test Location']);

        // Create a test CSV file
        $csvContent = "name,description,location\nTest Item,Test Description,Test Location";
        $file = UploadedFile::fake()->createWithContent('items.csv', $csvContent);

        $response = $this->actingAs($user)->post('/items-import', [
            'csv_file' => $file
        ]);

        $response->assertRedirect('/items');
        $response->assertSessionHas('success');

        Queue::assertPushed(ProcessItemsImport::class);
    }

    public function test_invalid_file_type_is_rejected()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('items.pdf', 100);

        $response = $this->actingAs($user)->post('/items-import', [
            'csv_file' => $file
        ]);

        $response->assertSessionHasErrors(['csv_file']);
    }

    public function test_missing_file_is_rejected()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/items-import', []);

        $response->assertSessionHasErrors(['csv_file']);
    }

    public function test_large_file_is_rejected()
    {
        $user = User::factory()->create();

        // Create a file larger than 2MB
        $file = UploadedFile::fake()->create('items.csv', 3000); // 3MB

        $response = $this->actingAs($user)->post('/items-import', [
            'csv_file' => $file
        ]);

        $response->assertSessionHasErrors(['csv_file']);
    }

    public function test_items_import_requires_authentication()
    {
        $response = $this->get('/items-import');
        $response->assertRedirect('/login');

        $response = $this->post('/items-import');
        $response->assertRedirect('/login');
    }
}
