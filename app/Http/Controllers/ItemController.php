<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportItemsRequest;
use App\Jobs\ProcessItemsImport;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
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
            'status' => 'required|in:registered,normal,gone',
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
            'status' => 'required|in:registered,normal,gone',
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
}
