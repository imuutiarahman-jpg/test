<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login', ['portal' => null]);
    }

    public function createPortal(?string $portal = null): View
    {
        return view('auth.login', ['portal' => $portal]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->role === 'master_admin') {
            return redirect()->intended(route('admin.portal', absolute: false));
        }

        $portal = $user?->portal;
        $route = match ($portal) {
            'qmas' => 'qmas.dashboard',
            'foodcourt' => 'foodcourt.dashboard',
            'sm' => 'sm.dashboard',
            default => 'dashboard',
        };

        return redirect()->intended(route($route, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
