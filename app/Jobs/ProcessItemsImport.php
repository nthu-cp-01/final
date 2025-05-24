<?php

namespace App\Jobs;

use App\Imports\ItemsImport;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessItemsImport implements ShouldQueue
{
    use Queueable;

    protected $filePath;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Process the import
            $import = new ItemsImport($this->userId);
            Excel::import($import, $this->filePath);

            // Clean up: delete the temporary file
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            // You could add notification logic here to inform the user
            // For example, sending an email or creating a notification
            
        } catch (\Exception $e) {
            // Clean up the file even if import fails
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }
            
            // Log the error or handle it as needed
            \Log::error('Items import failed: ' . $e->getMessage(), [
                'file_path' => $this->filePath,
                'user_id' => $this->userId
            ]);
            
            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Clean up the file if job fails
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
        
        // You could add notification logic here to inform the user of failure
    }
}
