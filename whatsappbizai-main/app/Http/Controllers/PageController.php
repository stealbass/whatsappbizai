<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function contactForm()
    {
        return view('contact', ['errors' => new \Illuminate\Support\ViewErrorBag()]);
    }

    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Store in database for now (no mail setup yet)
        \Illuminate\Support\Facades\DB::table('contact_messages')->insert([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'subject'   => $validated['subject'],
            'message'   => $validated['message'],
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        $msg = app()->getLocale() === 'fr'
            ? 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.'
            : 'Your message has been sent. We will reply within 24 hours.';

        return redirect(url('contact'))->with('contact_success', $msg);
    }
}
