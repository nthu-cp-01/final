<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index(LocationService $locationService)
    {

        $locations = Location::all()->map(function (Location $location) use ($locationService) {
            $data = $locationService->getThingShadow($location->deviceId, $location->shadowName);

            return array_merge($location->toArray(), [
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'ac_on' => $data['ac_is_enable'],
                'dehumidifier_on' => $data['dehumidifier_is_enable'],
            ]);
        });

        return Inertia::render('locations/Index', [
            'locations' => $locations
        ]);
    }

    /**
     * Show the form for creating a new location.
     */
    public function create()
    {
        return Inertia::render('locations/Create');
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'deviceId' => 'required|string|max:255',
            'shadowName' => 'required|string|max:255',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')
            ->with('message', 'Location created successfully.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location)
    {
        return Inertia::render('locations/Show', [
            'location' => $location
        ]);
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location)
    {
        return Inertia::render('locations/Edit', [
            'location' => $location
        ]);
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'deviceId' => 'required|string|max:255',
            'shadowName' => 'required|string|max:255',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('message', 'Location updated successfully.');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index')
            ->with('message', 'Location removed successfully.');
    }
}
