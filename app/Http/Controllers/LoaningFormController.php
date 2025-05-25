<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\LoaningForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoaningFormController extends Controller
{
    /**
     * Display a listing of the loaning forms.
     */
    public function index()
    {
        $loaningForms = LoaningForm::with(['item.owner', 'item.manager', 'applicant'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('loaning-forms/Index', [
            'loaningForms' => $loaningForms,
        ]);
    }

    /**
     * Show the form for creating a new loaning form.
     */
    public function create()
    {
        $items = Item::with(['owner', 'manager'])->get();

        return Inertia::render('loaning-forms/Create', [
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created loaning form in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $loaningForm = LoaningForm::create([
            'item_id' => $validated['item_id'],
            'applicant_id' => Auth::id(),
            'status' => 'requested',
        ]);

        return redirect()->route('loaning-forms.index')
            ->with('success', 'Loaning form submitted successfully!');
    }

    /**
     * Display the specified loaning form.
     */
    public function show(LoaningForm $loaningForm)
    {
        $loaningForm->load(['item.owner', 'item.manager', 'applicant']);

        return Inertia::render('loaning-forms/Show', [
            'loaningForm' => $loaningForm,
        ]);
    }

    /**
     * Show the form for editing the specified loaning form.
     */
    public function edit(LoaningForm $loaningForm)
    {
        if (!$loaningForm->canBeModified()) {
            return redirect()->route('loaning-forms.index')
                ->with('error', 'This loaning form cannot be modified.');
        }

        $items = Item::with(['owner', 'manager'])->get();

        return Inertia::render('loaning-forms/Edit', [
            'loaningForm' => $loaningForm,
            'items' => $items,
        ]);
    }

    /**
     * Update the specified loaning form in storage.
     */
    public function update(Request $request, LoaningForm $loaningForm)
    {
        if (!$loaningForm->canBeModified()) {
            return redirect()->route('loaning-forms.index')
                ->with('error', 'This loaning form cannot be modified.');
        }

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
        ]);

        $loaningForm->update($validated);

        return redirect()->route('loaning-forms.index')
            ->with('success', 'Loaning form updated successfully!');
    }

    /**
     * Remove the specified loaning form from storage.
     */
    public function destroy(LoaningForm $loaningForm)
    {
        if (!$loaningForm->canBeDeleted()) {
            return redirect()->route('loaning-forms.index')
                ->with('error', 'This loaning form cannot be deleted.');
        }

        $loaningForm->delete();

        return redirect()->route('loaning-forms.index')
            ->with('success', 'Loaning form deleted successfully!');
    }

    /**
     * Approve the specified loaning form.
     */
    public function approve(Request $request, LoaningForm $loaningForm)
    {
        if ($loaningForm->status !== 'requested') {
            return redirect()->route('loaning-forms.index')
                ->with('error', 'Only requested loaning forms can be approved.');
        }

        $loaningForm->update([
            'status' => 'approved',
        ]);

        // Update the status on item to be 'reserved'
        $loaningForm->item->update([
            'status' => 'reserved',
        ]);

        return redirect()->route('loaning-forms.index')
            ->with('success', 'Loaning form approved successfully!');
    }

    /**
     * Reject the specified loaning form.
     */
    public function reject(LoaningForm $loaningForm)
    {
        if ($loaningForm->status !== 'requested') {
            return redirect()->route('loaning-forms.index')
                ->with('error', 'Only requested loaning forms can be rejected.');
        }

        $loaningForm->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('loaning-forms.index')
            ->with('success', 'Loaning form rejected successfully!');
    }
}
