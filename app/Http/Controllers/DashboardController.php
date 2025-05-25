<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LoaningForm;
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

        // Get requested loaning forms for the dashboard
        $requestedLoaningForms = LoaningForm::with(['item.owner', 'item.manager', 'applicant'])
            ->where('status', 'requested')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'requestedLoaningForms' => $requestedLoaningForms
        ]);
    }
}
