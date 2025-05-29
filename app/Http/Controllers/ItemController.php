<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportItemsRequest;
use App\Jobs\ProcessItemsImport;
use App\Models\Item;
use App\Models\LoaningForm;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index()
    {
        $items = Item::with(['manager', 'location', 'owner'])->get();

        return Inertia::render('items/Index', [
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $locations = Location::all();
        $users = User::all();

        return Inertia::render('items/Create', [
            'locations' => $locations,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'owner_id' => 'required|exists:users,id',
            'status' => 'required|in:registered,normal,reserved,gone',
        ]);

        // If no manager_id or owner_id is provided, use the authenticated user
        if (empty($validated['manager_id'])) {
            $validated['manager_id'] = auth()->id();
        }

        if (empty($validated['owner_id'])) {
            $validated['owner_id'] = auth()->id();
        }

        $item = Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        return Inertia::render('items/Show', [
            'item' => $item->load(['manager', 'location', 'owner']),
        ]);
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $locations = Location::all();
        $users = User::all();

        return Inertia::render('items/Edit', [
            'item' => $item,
            'locations' => $locations,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'owner_id' => 'required|exists:users,id',
            'status' => 'required|in:registered,normal,reserved,gone',
        ]);

        $item->update($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Item deleted successfully.');
    }

    /**
     * Show the import form.
     */
    public function import()
    {
        return Inertia::render('items/Import');
    }

    /**
     * Process the CSV import.
     */
    public function processImport(ImportItemsRequest $request)
    {
        $file = $request->file('csv_file');

        // Store the file temporarily
        $filePath = $file->store('imports', 'local');

        // Dispatch the job to process the import
        ProcessItemsImport::dispatch($filePath, auth()->id());

        return redirect()
            ->route('items.index')
            ->with('success', 'CSV import has been queued for processing. Items will appear shortly.');
    }

    /**
     * Handle IoT device scanning endpoint.
     */
    public function scan(Request $request): JsonResponse
    {
        // Validate the request payload
        $validated = $request->validate([
            'id' => 'required|integer|exists:items,id',
        ]);

        $itemId = $validated['id'];
        $scanningUser = $request->user();

        // Find the item with related models
        $item = Item::findOrFail($itemId);

        // Handle different item statuses
        switch ($item->status) {
            case 'gone':
                // If scanning user is manager, set to normal
                if ($scanningUser->id === $item->manager_id) {
                    $item->update(['status' => 'normal']);
                    return response()->json(['message' => 'Finally found ya boi!'], 200);
                }
            case 'normal':
                // If scanning user is manager, return 200
                if ($scanningUser->id === $item->manager_id) {
                    return response()->json(['message' => "I mean, it's already there, but okay..."], 200);
                }
                break;

            case 'registered':
                // If scanning user is manager, set to 'normal'
                if ($scanningUser->id === $item->manager_id) {
                    $item->update(['status' => 'normal']);
                    return response()->json(['message' => "You're mine!"], 200);
                }
                break;

            case 'reserved':
                // Look up related LoaningForm for scanning user
                $loaningForm = LoaningForm::where('item_id', $itemId)
                    ->where('applicant_id', $scanningUser->id)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($loaningForm) {
                    // Check if we can fill start_at (loan action)
                    if (is_null($loaningForm->start_at)) {
                        $loaningForm->update(['start_at' => now()]);
                        $item->update(['owner_id' => $scanningUser->id]);
                        return response()->json(['message' => 'Item loaned successfully'], 200);
                    }

                    // Check if we can fill end_at (return action)
                    if (is_null($loaningForm->end_at)) {
                        $loaningForm->update(['end_at' => now()]);
                        $item->update(['owner_id' => $item->manager_id]);
                        return response()->json(['message' => 'Item returned successfully'], 200);
                    }
                }
                break;
        }

        // Debug
        if ($scanningUser->id === $item->manager_id) {
            return response()->json(['message' => 'I know you are manager of the item, but what are you doing here?'], 401);
        }

        // Default case: unauthorized
        return response()->json(['message' => 'Unauthorized scan'], 401);
    }

    /**
     * Display the QR codes for the selected items.
     */
    public function qrCodes(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:items,id',
        ]);

        $items = Item::with('location')->whereIn('id', $validated['items'])->get();

        if ($items->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No items selected for QR code generation.');
        }

        return Inertia::render('items/QrCodes', [
            'items' => $items,
        ]);
    }
}
