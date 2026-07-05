<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect(url('dashboard'));
        }
        return view('auth.login', ['errors' => new \Illuminate\Support\ViewErrorBag()]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect(url('/admin'));
            }
            return redirect()->intended(url('dashboard'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(url('/'));
    }

    // ─── GOOGLE OAUTH ────────────────────────────────────────────────────

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect(url('login'))->with('error', 'Impossible de se connecter avec Google. Veuillez réessayer.');
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect(url('login'))->with('error', 'Impossible de récupérer votre email Google.');
        }

        // Check if user already exists with this email
        $user = User::where('email', $email)->first();

        if ($user) {
            // User exists — log them in
            Auth::login($user, true);
            $request = request();
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect(url('/admin'));
            }
            return redirect(url('dashboard'));
        }

        // New user — create account with business
        $name = $googleUser->getName() ?? explode('@', $email)[0];

        $business = Business::create([
            'name'       => $name . "'s Business",
            'owner_name' => $name,
            'email'      => $email,
            'currency'   => 'XAF',
            'city'       => '',
            'country'    => 'CM',
            'timezone'   => 'Africa/Douala',
            'plan'       => 'free',
            'is_active'  => true,
        ]);

        $user = User::create([
            'business_id' => $business->id,
            'name'        => $name,
            'email'       => $email,
            'password'    => Hash::make(Str::random(32)),
            'role'        => 'user',
        ]);

        Auth::login($user, true);
        $request = request();
        $request->session()->regenerate();

        return redirect(url('dashboard'))->with('success', 'Bienvenue ! Votre compte a été créé automatiquement.');
    }
}
