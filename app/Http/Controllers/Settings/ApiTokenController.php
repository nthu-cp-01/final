<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ApiTokenController extends Controller
{
    /**
     * Show the user's API token settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/ApiTokens', [
            'tokens' => auth()->user()->tokens()->select(['id', 'name', 'abilities', 'last_used_at', 'created_at'])->get(),
            'newToken' => session('newToken'),
        ]);
    }

    /**
     * Create a new API token.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['array'],
            'abilities.*' => ['string'],
        ]);

        // Default abilities if none provided
        $abilities = $validated['abilities'] ?? ['*'];

        $token = $request->user()->createToken(
            $validated['name'],
            $abilities
        );

        return redirect()->route('api-tokens.edit')->with([
            'newToken' => [
                'id' => $token->accessToken->id,
                'name' => $validated['name'],
                'token' => $token->plainTextToken,
            ]
        ]);
    }

    /**
     * Delete an API token.
     */
    public function destroy(Request $request, string $tokenId): RedirectResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            throw ValidationException::withMessages([
                'token' => 'Token not found.',
            ]);
        }

        $token->delete();

        return back()->with([
            'flash' => [
                'type' => 'success',
                'message' => 'API token deleted successfully.',
            ]
        ]);
    }
}
