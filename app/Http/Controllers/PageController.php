<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Store in database
        \Illuminate\Support\Facades\DB::table('contact_messages')->insert([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'subject'   => $validated['subject'],
            'message'   => $validated['message'],
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        // Envoie une notification à l'admin
        try {
            $adminEmail = config('mail.from.address', 'hello@whatsappbizai.com');
            Mail::to($adminEmail)->send(new ContactFormMail(
                senderName: $validated['name'],
                senderEmail: $validated['email'],
                subject: $validated['subject'],
                message: $validated['message'],
            ));
        } catch (\Exception $e) {
            Log::warning('Contact form email failed', ['error' => $e->getMessage()]);
        }

        $msg = app()->getLocale() === 'fr'
            ? 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.'
            : 'Your message has been sent. We will reply within 24 hours.';

        return redirect(url('contact'))->with('contact_success', $msg);
    }
}
