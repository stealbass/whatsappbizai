<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    public function start(User $user)
    {
        $superAdmin = Auth::user();

        if (!$superAdmin || !$superAdmin->is_super_admin) {
            abort(403, 'Only super-admins can impersonate.');
        }

        if (!$user->is_active) {
            return back()->with('error', 'This user account is disabled.');
        }

        if ($user->id === $superAdmin->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Save super-admin ID in a cookie (survives session invalidation)
        Cookie::queue('impersonator_id', $superAdmin->id, 120); // 2 hours

        // Fully logout and invalidate old session
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        // Login as the target user
        Auth::guard('web')->login($user);

        // Update last login
        $user->forceFill(['last_login_at' => now()])->save();

        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        return redirect('/dashboard');
    }

    public function leave(Request $request)
    {
        $impersonatorId = $request->cookie('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('home');
        }

        $superAdmin = User::find($impersonatorId);

        if (!$superAdmin || !$superAdmin->is_super_admin) {
            Cookie::queue(Cookie::forget('impersonator_id'));
            return redirect()->route('home');
        }

        // Fully logout and invalidate
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();

        // Login back as super-admin
        Auth::guard('web')->login($superAdmin);

        // Clear the impersonation cookie
        Cookie::queue(Cookie::forget('impersonator_id'));

        return redirect()->route('filament.super-admin.pages.dashboard');
    }
}
