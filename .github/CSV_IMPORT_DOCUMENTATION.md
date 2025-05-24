# CSV Import Feature Documentation

## Overview
The CSV Import feature allows users to upload CSV files to bulk import items into the inventory management system. The import process is handled asynchronously using Laravel's queue system to prevent timeout issues for large files.

## Implementation Details

### Backend Components

#### 1. Import Class (`app/Imports/ItemsImport.php`)
- **Purpose**: Handles the actual parsing and importing of CSV data
- **Features**:
  - Uses Laravel Excel package for CSV processing
  - Validates required fields (name, location)
  - Supports batch processing (100 items per batch)
  - Handles chunked reading for memory efficiency
  - Provides default values for optional fields
  - Maps location names to database IDs
  - Maps user names/emails to user IDs

#### 2. Job Class (`app/Jobs/ProcessItemsImport.php`)
- **Purpose**: Handles the asynchronous processing of CSV imports
- **Features**:
  - Processes import in background
  - Automatic file cleanup after processing
  - Error handling with logging
  - Prevents prolonged user wait times

#### 3. Request Validation (`app/Http/Requests/ImportItemsRequest.php`)
- **Purpose**: Validates the uploaded CSV file
- **Validation Rules**:
  - File is required
  - Must be CSV or TXT format
  - Maximum file size: 2MB
  - Custom error messages for better UX

#### 4. Controller Methods (`app/Http/Controllers/ItemController.php`)
- **Routes Added**:
  - `GET /items-import` - Show import form
  - `POST /items-import` - Process import file
- **Methods**:
  - `import()` - Display import form
  - `processImport()` - Handle file upload and queue job

### Frontend Components

#### 1. Import Page (`resources/js/pages/items/Import.vue`)
- **Features**:
  - File upload form with drag-and-drop support
  - Real-time validation feedback
  - Progress indicators
  - Detailed format instructions
  - Example CSV format display

#### 2. Items Index Enhancement
- **Added**: Import CSV button next to "Add Item"
- **Styling**: Consistent with existing UI patterns

### Database Schema
No additional database changes required. Uses existing:
- `items` table for storing imported items
- `jobs` table for queue management
- `locations` table for location relationships
- `users` table for manager/owner relationships

## CSV Format Requirements

### Required Columns
- `name` - Item name (string, max 255 characters)
- `location` - Location name (must exist in locations table)

### Optional Columns
- `description` - Item description (string)
- `purchase_date` - Purchase date (YYYY-MM-DD format)
- `manager` - Manager name or email (defaults to importer if empty)
- `owner` - Owner name or email (defaults to importer if empty)
- `status` - Item status: "registered", "normal", or "gone" (defaults to "registered")

### Example CSV
```csv
name,description,purchase_date,location,manager,owner,status
"Laptop Dell XPS 13","High-performance laptop for development",2024-01-15,"Testing Location in R744","","",normal
"Monitor Samsung 27 inch","4K display monitor",2024-02-01,"Testing Location in R744","","",registered
"Wireless Mouse","Ergonomic wireless mouse",2024-01-20,"Testing Location in R744","","",normal
```

## Error Handling

### Validation Errors
- Invalid file format
- File size too large
- Missing required columns
- Invalid location names
- Invalid user references

### Processing Errors
- Database connection issues
- Memory limitations
- File corruption
- Queue processing failures

## Performance Considerations

### Batch Processing
- Items are processed in batches of 100
- Reduces memory usage for large files
- Prevents database timeout issues

### Queue System
- Uses Laravel's database queue driver
- Processes imports in background
- Automatic retry mechanism for failed jobs
- File cleanup after processing

### Memory Management
- Chunked reading of CSV files
- Configurable chunk size (100 rows)
- Efficient memory usage for large files

## Security Features

### File Validation
- Strict MIME type checking
- File size limitations
- File extension validation

### Access Control
- Authentication required
- User authorization checks
- File cleanup after processing

### Data Validation
- Input sanitization
- SQL injection prevention
- XSS protection through Inertia.js

## Usage Instructions

### For End Users
1. Navigate to Items → Import CSV
2. Click "Choose File" or drag CSV file to upload area
3. Review format requirements if needed
4. Click "Import Items" to start processing
5. Items will appear in the list after processing completes

### For Administrators
1. Ensure queue worker is running: `php artisan queue:work`
2. Monitor queue status: `php artisan queue:monitor`
3. Check logs for import errors: `storage/logs/laravel.log`
4. Clear failed jobs if needed: `php artisan queue:clear`

## Configuration

### Queue Configuration
Default queue driver: `database`
Queue table: `jobs`
Retry attempts: 3
Timeout: 60 seconds

### File Upload Configuration
Max file size: 2MB
Allowed types: CSV, TXT
Storage location: `storage/app/imports` (temporary)

## Troubleshooting

### Common Issues
1. **Import not processing**: Check queue worker is running
2. **File upload errors**: Verify file size and format
3. **Location not found**: Ensure location exists in database
4. **User not found**: Verify user email/name exists

### Debug Commands
```bash
# Check queue status
php artisan queue:monitor

# Process queue manually
php artisan queue:work --once

# Clear failed jobs
php artisan queue:clear

# View logs
tail -f storage/logs/laravel.log
```

## Future Enhancements

### Potential Improvements
1. Real-time import progress tracking
2. Import history and statistics
3. Email notifications on completion
4. CSV template download
5. Import validation preview
6. Duplicate detection and handling
7. Custom field mapping interface

### Scalability Considerations
1. Redis queue driver for high volume
2. File storage optimization
3. Database indexing improvements
4. Horizontal scaling support
