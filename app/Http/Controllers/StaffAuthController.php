<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StaffLoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('staff.dashboard');
        }

        return view('staff.auth.login');
    }

    public function store(StaffLoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'is_active' => true])) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
        }

        if (! in_array($request->user()->role, [UserRole::Admin, UserRole::Petugas], true)) {
            Auth::logout();

            return back()->withErrors(['email' => 'Akun ini tidak memiliki akses petugas.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('staff.dashboard'));
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('staff.login');
    }
}
