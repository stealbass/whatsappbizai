<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function business()
    {
        $user = Auth::user();
        $business = $user->business;
        return view('client.settings.business', compact('user', 'business'));
    }

    public function businessUpdate(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'owner_name'  => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:500',
            'city'        => 'nullable|string|max:100',
            'country'     => 'nullable|string|max:2',
            'currency'    => 'nullable|string|max:3',
            'timezone'    => 'nullable|string|max:50',
            'invoice_prefix' => 'nullable|string|max:10',
            'quote_prefix'   => 'nullable|string|max:10',
        ]);

        $business->update($data);

        return redirect(url('client/settings/business'))->with('success', 'Paramètres entreprise mis à jour.');
    }

    public function whatsapp()
    {
        $user = Auth::user();
        $business = $user->business;
        return view('client.settings.whatsapp', compact('user', 'business'));
    }

    public function whatsappUpdate(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;

        $data = $request->validate([
            'whatsapp_phone_number_id'      => 'nullable|string|max:100',
            'whatsapp_access_token'         => 'nullable|string|max:500',
            'whatsapp_business_account_id'  => 'nullable|string|max:100',
            'gemini_system_prompt'          => 'nullable|string|max:5000',
        ]);

        $business->update($data);

        return redirect(url('client/settings/whatsapp'))->with('success', 'Configuration WhatsApp & IA mise à jour.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('client.settings.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($data);

        return redirect(url('client/settings/profile'))->with('success', 'Profil mis à jour.');
    }

    public function password()
    {
        $user = Auth::user();
        return view('client.settings.password', compact('user'));
    }

    public function passwordUpdate(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect(url('client/settings/profile'))->with('success', 'Mot de passe modifié.');
    }

    public function billing()
    {
        $user = Auth::user();
        $business = $user->business;
        $subscriptions = $business->subscriptions()->orderByDesc('created_at')->get();
        return view('client.settings.billing', compact('user', 'business', 'subscriptions'));
    }
}
