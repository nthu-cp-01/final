<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index()
    {
        $locations = Location::all()->map(function ($location) {
            // Add dummy temperature, humidity data, and device status
            return array_merge($location->toArray(), [
                'temperature' => rand(18, 30),  // Random temperature between 18-30°C
                'humidity' => rand(30, 80),     // Random humidity between 30-80%
                'ac_on' => (bool)rand(0, 1),    // Random boolean for AC status
                'humidifier_on' => (bool)rand(0, 1), // Random boolean for humidifier status
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
            'deviceId' => 'required|string|unique:locations,deviceId,' . $location->id,
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
