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

    /**
     * Toggle air conditioner state for a location.
     */
    public function toggleAc(Location $location, LocationService $locationService)
    {
        try {
            // Get current state
            $currentState = $locationService->getThingShadow($location->deviceId, $location->shadowName);
            
            // Toggle the AC state
            $newAcState = !($currentState['ac_is_enable'] ?? false);
            
            // Update the shadow with new state
            $locationService->updateThingShadow(
                $location->deviceId, 
                $location->shadowName, 
                ['ac_is_enable' => $newAcState]
            );

            // Flash success message using Inertia's shared data
            return redirect()->back()->with([
                'success' => true,
                'message' => 'AC ' . ($newAcState ? 'enabled' : 'disabled') . ' successfully.',
                'new_state' => $newAcState
            ]);
        } catch (\Exception $e) {
            // Try to parse the error details if available
            $errorDetails = [];
            try {
                $errorDetails = json_decode($e->getMessage(), true) ?: ['message' => $e->getMessage()];
            } catch (\Exception $jsonError) {
                $errorDetails = ['message' => $e->getMessage()];
            }

            // Flash error message using Inertia's shared data
            return redirect()->back()->with([
                'error' => true,
                'message' => 'Failed to toggle AC',
                'errorDetails' => $errorDetails
            ]);
        }
    }

    /**
     * Toggle dehumidifier state for a location.
     */
    public function toggleDehumidifier(Location $location, LocationService $locationService)
    {
        try {
            // Get current state
            $currentState = $locationService->getThingShadow($location->deviceId, $location->shadowName);
            
            // Toggle the dehumidifier state
            $newDehumidifierState = !($currentState['dehumidifier_is_enable'] ?? false);
            
            // Update the shadow with new state
            $locationService->updateThingShadow(
                $location->deviceId, 
                $location->shadowName, 
                ['dehumidifier_is_enable' => $newDehumidifierState]
            );

            // Flash success message using Inertia's shared data
            return redirect()->back()->with([
                'success' => true,
                'message' => 'Dehumidifier ' . ($newDehumidifierState ? 'enabled' : 'disabled') . ' successfully.',
                'new_state' => $newDehumidifierState
            ]);
        } catch (\Exception $e) {
            // Try to parse the error details if available
            $errorDetails = [];
            try {
                $errorDetails = json_decode($e->getMessage(), true) ?: ['message' => $e->getMessage()];
            } catch (\Exception $jsonError) {
                $errorDetails = ['message' => $e->getMessage()];
            }

            // Flash error message using Inertia's shared data
            return redirect()->back()->with([
                'error' => true,
                'message' => 'Failed to toggle dehumidifier',
                'errorDetails' => $errorDetails
            ]);
        }
    }
}
