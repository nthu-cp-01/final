<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $importerId;
    protected $errors = [];

    public function __construct($importerId)
    {
        $this->importerId = $importerId;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find location by name
        $location = Location::where('name', $row['location'])->first();
        if (!$location) {
            // Skip this row if location doesn't exist
            return null;
        }

        // Find manager by name or email (optional, defaults to importer)
        $manager = null;
        if (!empty($row['manager'])) {
            $manager = User::where('name', $row['manager'])
                          ->orWhere('email', $row['manager'])
                          ->first();
        }
        $managerId = $manager ? $manager->id : $this->importerId;

        // Find owner by name or email (optional, defaults to importer)
        $owner = null;
        if (!empty($row['owner'])) {
            $owner = User::where('name', $row['owner'])
                        ->orWhere('email', $row['owner'])
                        ->first();
        }
        $ownerId = $owner ? $owner->id : $this->importerId;

        // Parse purchase date
        $purchaseDate = null;
        if (!empty($row['purchase_date'])) {
            try {
                $purchaseDate = Carbon::parse($row['purchase_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $purchaseDate = now()->format('Y-m-d');
            }
        } else {
            $purchaseDate = now()->format('Y-m-d');
        }

        // Set default status
        $status = !empty($row['status']) ? strtolower($row['status']) : 'registered';
        if (!in_array($status, ['registered', 'normal', 'gone'])) {
            $status = 'registered';
        }

        return new Item([
            'name' => $row['name'] ?? 'Unnamed Item',
            'description' => $row['description'] ?? '',
            'purchase_date' => $purchaseDate,
            'location_id' => $location->id,
            'manager_id' => $managerId,
            'owner_id' => $ownerId,
            'status' => $status,
        ]);
    }

    /**
     * Validation rules for each row
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'location' => 'required|string|exists:locations,name',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Item name is required.',
            'location.required' => 'Location is required.',
            'location.exists' => 'The specified location does not exist.',
        ];
    }

    /**
     * Batch insert size
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk reading size
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Get import errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
