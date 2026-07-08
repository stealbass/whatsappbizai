<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function show()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'owner_name'    => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'city'          => 'required|string|max:100',
        ]);

        // Crée le business
        $business = Business::create([
            'name'       => $data['business_name'],
            'owner_name' => $data['owner_name'],
            'email'      => $data['email'],
            'city'       => $data['city'],
            'country'    => 'CM',
            'currency'   => 'XAF',
            'timezone'   => 'Africa/Douala',
            'plan'       => 'free',
            'is_active'  => true,
        ]);

        // Crée l'utilisateur lié au business
        $user = User::create([
            'business_id' => $business->id,
            'name'        => $data['owner_name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'user',
        ]);

        Auth::login($user);

        // Envoie l'email de bienvenue
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email failed', ['error' => $e->getMessage()]);
        }

        return redirect(url('dashboard'))->with('success', 'Bienvenue sur WhatsAppBizAI ! Configurez votre compte WhatsApp pour démarrer.');
    }
}
