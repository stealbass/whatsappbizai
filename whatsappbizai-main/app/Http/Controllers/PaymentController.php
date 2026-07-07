<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Payment;
use App\Services\FlutterwaveService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private FlutterwaveService $flutterwave) {}

    /**
     * Page de tarification / checkout
     */
    public function pricing()
    {
        $plans = \App\Models\Subscription::$plans;
        return view('pricing', compact('plans'));
    }

    /**
     * Initie un paiement Flutterwave
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'plan'     => 'required|in:starter,business,pro',
            'cycle'    => 'required|in:monthly,yearly',
            'currency' => 'nullable|string|max:5',
        ]);

        $business = auth()->user()->business;
        if (!$business) {
            return redirect()->route('register.show')->with('error', 'Créez votre compte entreprise d\'abord.');
        }

        // Use user-selected currency, fallback to business currency, then XAF
        $currency = $request->input('currency') ?? $business->currency ?? 'XAF';

        $result = $this->flutterwave->initializePayment($business, $request->plan, $request->cycle, $currency);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Erreur lors de l\'initialisation du paiement.');
        }

        return redirect($result['payment_url']);
    }

    /**
     * Callback Flutterwave après paiement
     */
    public function callback(Request $request)
    {
        $status        = $request->get('status');
        $transactionId = $request->get('transaction_id');
        $txRef         = $request->get('tx_ref');

        if ($status !== 'successful' || !$transactionId) {
            return redirect()->route('payment.pricing')
                ->with('error', 'Paiement annulé ou échoué. Réessayez.');
        }

        $verification = $this->flutterwave->verifyPayment($transactionId);

        if (!$verification['success']) {
            return redirect()->route('payment.pricing')
                ->with('error', 'Impossible de vérifier le paiement. Contactez le support.');
        }

        $payment = Payment::where('reference', $txRef)->first();
        if ($payment && $payment->status === 'pending') {
            $payment->update(['flutterwave_tx_id' => $transactionId ?? $verification['tx_id']]);
            $subscription = $this->flutterwave->activateSubscription($payment);

            return redirect(url('client/settings/billing'))->with('success',
                "🎉 Abonnement {$subscription->plan} activé ! Merci pour votre confiance."
            );
        }

        return redirect(url('client/settings/billing'))->with('info', 'Ce paiement a déjà été traité.');
    }

    /**
     * Webhook Flutterwave (events asynchrones)
     */
    public function webhook(Request $request)
    {
        $secret = config('flutterwave.webhook_secret');
        $hash   = $request->header('verif-hash');

        if ($hash !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event = $request->json()->all();

        if (($event['event'] ?? '') === 'charge.completed' && ($event['data']['status'] ?? '') === 'successful') {
            $txRef   = $event['data']['tx_ref'] ?? '';
            $payment = Payment::where('reference', $txRef)->where('status', 'pending')->first();

            if ($payment) {
                $payment->update(['flutterwave_tx_id' => (string) ($event['data']['id'] ?? '')]);
                $this->flutterwave->activateSubscription($payment);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Formulaire de paiement manuel (MoMo, Orange, virement)
     */
    public function manualForm()
    {
        $plans = \App\Models\Subscription::$plans;
        return view('payment.manual', compact('plans'));
    }

    /**
     * Soumission du paiement manuel
     */
    public function manualStore(Request $request)
    {
        $request->validate([
            'plan'         => 'required|in:starter,business,pro',
            'cycle'        => 'required|in:monthly,yearly',
            'method'       => 'required|in:mtn_momo,orange_money,wave,bank_transfer,other',
            'reference'    => 'required|string|max:100',
            'phone_number' => 'nullable|string|max:30',
            'screenshot'   => 'nullable|image|max:5120',
            'notes'        => 'nullable|string|max:500',
        ]);

        $business = auth()->user()->business;
        $plans    = \App\Models\Subscription::$plans;
        $plan     = $request->plan;
        $cycle    = $request->cycle;
        $amount   = $cycle === 'yearly'
            ? $plans[$plan]['price_xaf_yearly']
            : $plans[$plan]['price_xaf_monthly'];

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment-proofs', 'public');
        }

        Payment::create([
            'business_id'    => $business->id,
            'method'         => $request->method,
            'status'         => 'pending',
            'plan'           => $plan,
            'billing_cycle'  => $cycle,
            'amount'         => $amount,
            'currency'       => $business->currency ?? 'XAF',
            'reference'      => $request->reference,
            'phone_number'   => $request->phone_number,
            'screenshot_path'=> $screenshotPath,
            'notes'          => $request->notes,
        ]);

        return redirect(url('client/settings/billing'))
            ->with('success', 'Votre preuve de paiement a été soumise. L\'équipe vérifiera sous 24h et activera votre abonnement.');
    }
}
