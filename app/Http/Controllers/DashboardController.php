<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'locations_count' => Location::count(),
            'items_count' => Item::count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
