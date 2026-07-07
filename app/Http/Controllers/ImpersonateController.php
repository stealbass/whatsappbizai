<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ImpersonateController extends Controller
{
    /**
     * Single route handles both start and leave:
     * - GET /impersonate/{id}?save_current=true  → start impersonation
     * - GET /impersonate/{id}                     → leave (go back to admin)
     */
    public function __invoke(Request $request, int $id)
    {
        $currentUser = Auth::user();

        // Must be super-admin OR already impersonating (to go back)
        if ((!$currentUser || !$currentUser->is_super_admin) && empty(session('previous_user_id'))) {
            abort(403);
        }

        $previousUserId   = $currentUser?->id;
        $previousUsername  = $currentUser?->name;

        // Flush entire session (like ultimate project)
        session()->flush();

        // If starting impersonation, save who we were
        if ($request->has('save_current')) {
            session([
                'previous_user_id'   => $previousUserId,
                'previous_username'  => $previousUsername,
            ]);
        }

        // Login as the target user
        Auth::loginUsingId($id);

        // Update last login
        $target = User::find($id);
        if ($target) {
            $target->forceFill(['last_login_at' => now()])->save();
        }

        return redirect()->route('filament.admin.pages.dashboard');
    }
}
