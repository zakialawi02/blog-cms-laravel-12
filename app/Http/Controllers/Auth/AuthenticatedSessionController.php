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
    public function create(Request $request): View|RedirectResponse
    {
        $previous = url()->previous();

        // Cek apakah belum ada ?redirect di URL dan previous bukan root/login
        if (!$request->has('redirect') && !in_array($previous, [url('/'), route('login')])) {
            $loginUrl = route('login', ['redirect' => $previous]);
            return redirect($loginUrl)->withInput($request->all())->with($request->session()->all());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // dd(url()->previous());
        $request->authenticate();

        $request->session()->regenerate();

        // Get the previous URL
        $url = url()->previous();
        // Parse the URL to get the query parameters
        $parsedUrl = parse_url($url);
        // Check if there is a 'redirect' query parameter
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
            $redirectTo = $queryParams['redirect'] ?? null;
            // If redirectTo exists and it's not just the root '/', redirect accordingly
            if ($redirectTo && $redirectTo !== '/') {
                return redirect($redirectTo);
            }
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Simpan URL sebelumnya sebelum logout (tangkap jika ada redirect)
        $previousUrl = url()->previous();

        // Cek apakah ada parameter 'redirect' pada URL sebelumnya
        if ($request->has('redirect')) {
            $previousUrl = $request->query('redirect');
        }

        // Logout user
        Auth::guard('web')->logout();

        // Invalidate the session and regenerate the CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman sebelumnya, jika tidak ada redirect, kembali ke root '/'
        return redirect($previousUrl ?: '/');
    }
}
