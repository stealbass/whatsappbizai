<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FlutterwaveService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('flutterwave.secret_key');
        $this->publicKey = config('flutterwave.public_key');
    }

    /**
     * Convert XAF amount to target currency using config rates.
     */
    public function convertAmount(float $amountXaf, string $targetCurrency): float
    {
        $currencies = config('flutterwave.currencies', []);
        $rate = $currencies[$targetCurrency]['rate'] ?? 1;
        return round($amountXaf * $rate);
    }

    /**
     * Get the Flutterwave-accepted currency code.
     * Some currencies (XOF) map to different codes on Flutterwave.
     */
    public function flutterwaveCurrency(string $currency): string
    {
        // Flutterwave uses "XAF" for both BEAC and BCEAO zones,
        // but some acquirers may need specific codes.
        $map = [
            'XOF' => 'XAF', // Flutterwave accepts XAF for West African CFA
        ];
        return $map[$currency] ?? $currency;
    }

    /**
     * Get country code for a currency (used for Flutterwave auto-detection).
     */
    public function countryForCurrency(string $currency): string
    {
        $currencies = config('flutterwave.currencies', []);
        return $currencies[$currency]['country'] ?? '';
    }

    /**
     * Initialise un paiement Flutterwave — retourne l'URL de paiement.
     * Accepts optional $currency override from user preference.
     */
    public function initializePayment(Business $business, string $plan, string $cycle, ?string $currency = null): array
    {
        $plans     = Subscription::$plans[$plan];
        $amountXaf = $cycle === 'yearly' ? $plans['price_xaf_yearly'] : $plans['price_xaf_monthly'];
        $txRef     = 'WBAI-' . strtoupper(Str::random(12)) . '-' . time();

        // Use user-selected currency, fallback to business currency, then XAF
        $displayCurrency = $currency ?? $business->currency ?? 'XAF';

        // Convert amount to target currency
        $amount = $this->convertAmount($amountXaf, $displayCurrency);

        // Get Flutterwave-compatible currency code
        $fwCurrency = $this->flutterwaveCurrency($displayCurrency);

        // Get country code for auto-detection
        $country = $this->countryForCurrency($displayCurrency);

        $payload = [
            'tx_ref'          => $txRef,
            'amount'          => $amount,
            'currency'        => $fwCurrency,
            'redirect_url'    => route('payment.callback'),
            'customer'        => [
                'email' => $business->email,
                'name'  => $business->owner_name ?? $business->name,
                'phone_number' => $business->phone ?? '',
            ],
            'customizations'  => [
                'title'       => 'WhatsAppBizAI',
                'description' => "Abonnement {$plan} (" . ($cycle === 'yearly' ? 'annuel' : 'mensuel') . ')',
                'logo'        => url('/images/logo.png'),
            ],
            'meta' => [
                'business_id' => $business->id,
                'plan'        => $plan,
                'cycle'       => $cycle,
                'currency'    => $displayCurrency,
                'amount_xaf'  => $amountXaf,
            ],
        ];

        // Add country if available — helps Flutterwave auto-detect payment methods
        if ($country) {
            $payload['customer']['country'] = $country;
        }

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/payments", $payload);

        if ($response->successful() && $response->json('status') === 'success') {
            // Save payment record with both XAF amount (for reference) and converted amount
            Payment::create([
                'business_id'           => $business->id,
                'method'                => 'flutterwave',
                'status'                => 'pending',
                'plan'                  => $plan,
                'billing_cycle'         => $cycle,
                'amount'                => $amount,
                'currency'              => $displayCurrency,
                'reference'             => $txRef,
            ]);

            return [
                'success'     => true,
                'payment_url' => $response->json('data.link'),
                'tx_ref'      => $txRef,
                'amount'      => $amount,
                'currency'    => $displayCurrency,
            ];
        }

        return ['success' => false, 'message' => $response->json('message') ?? 'Erreur Flutterwave'];
    }

    /**
     * Vérifie un paiement après callback
     */
    public function verifyPayment(string $transactionId): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transactions/{$transactionId}/verify");

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'Erreur de vérification'];
        }

        $data   = $response->json('data');
        $status = $data['status'] ?? 'failed';

        return [
            'success'  => $status === 'successful',
            'status'   => $status,
            'amount'   => $data['amount'] ?? 0,
            'currency' => $data['currency'] ?? 'XAF',
            'tx_ref'   => $data['tx_ref'] ?? '',
            'tx_id'    => $data['id'] ?? '',
            'meta'     => $data['meta'] ?? [],
        ];
    }

    /**
     * Active l'abonnement après paiement validé
     */
    public function activateSubscription(Payment $payment): Subscription
    {
        $cycle = $payment->billing_cycle;
        $end   = $cycle === 'yearly' ? now()->addYear() : now()->addMonth();

        $subscription = Subscription::create([
            'business_id'        => $payment->business_id,
            'plan'               => $payment->plan,
            'status'             => 'active',
            'billing_cycle'      => $cycle,
            'starts_at'          => now(),
            'ends_at'            => $end,
            'flutterwave_tx_ref' => $payment->reference,
            'amount_paid'        => $payment->amount,
            'currency'           => $payment->currency,
        ]);

        // Met à jour le plan du business
        $payment->business->update(['plan' => $payment->plan]);
        $payment->update(['status' => 'verified', 'subscription_id' => $subscription->id]);

        return $subscription;
    }
}
