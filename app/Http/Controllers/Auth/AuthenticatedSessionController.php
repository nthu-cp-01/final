<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): \Illuminate\Http\Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Inertia::location(Socialite::driver('cognito')->logoutCognitoUser());
    }

    /**
     * Redirect to Cognito login.
     */
    public function cognito(Request $request): \Illuminate\Http\Response
    {
        return Inertia::location(
            Socialite::driver('cognito')->redirect()->getTargetUrl()
        );
    }

    /**
     * Handle the callback from Cognito.
     */
    public function cognitoCallback(Request $request): RedirectResponse
    {
        $cognitoUser = Socialite::driver('cognito')->stateless()->user();

        // Handle the user creation
        $user = User::firstOrCreate(
            ['email' => $cognitoUser->email],
            [
                'provider_id' => $cognitoUser->id,
                'name' => $cognitoUser->user['name'] ?? 'Cognito User',
                'email' => $cognitoUser->user['email'],
                'email_verified_at' => $cognitoUser->user['email_verified'] ? now() : null,
            ]
        );

        // Log in the user
        Auth::guard('web')->login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
