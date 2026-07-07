<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Invoice;
use App\Models\Contact;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    public function __construct(
        private readonly WhatsAppService $whatsapp,
        private readonly GeminiService   $gemini,
    ) {}

    /**
     * Envoie les relances pour toutes les factures en retard
     * À appeler via un scheduled command (Artisan)
     */
    public function sendOverdueReminders(Business $business): int
    {
        $sent = 0;

        $overdueInvoices = Invoice::with(['contact'])
            ->where('business_id', $business->id)
            ->where('status', 'sent')
            ->where('due_date', '<', now())
            ->whereColumn('paid_amount', '<', 'total')
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $contact = $invoice->contact;

            if (!$contact->whatsapp_number) continue;

            $daysLate = now()->diffInDays($invoice->due_date);
            $balance  = number_format($invoice->balance, 0, ',', ' ');
            $currency = $invoice->currency;

            $message = $this->gemini->draftReminder($business, $invoice)
                ?: $this->buildReminderMessage($invoice, $daysLate, $balance, $currency, $business);

            $ok = $this->whatsapp->sendText(
                $contact->whatsapp_number,
                $message,
                $business->whatsapp_phone_number_id,
                $business->whatsapp_access_token
            );

            if ($ok) {
                $sent++;
                Log::info("Relance envoyée", [
                    'invoice' => $invoice->number,
                    'contact' => $contact->name,
                ]);
            }
        }

        return $sent;
    }

    /**
     * Envoie une relance manuelle pour une facture spécifique
     */
    public function sendManualReminder(Invoice $invoice): bool
    {
        $invoice->load(['business', 'contact']);
        $business = $invoice->business;
        $contact  = $invoice->contact;

        $daysLate = max(0, now()->diffInDays($invoice->due_date));
        $balance  = number_format($invoice->balance, 0, ',', ' ');

        $message = $this->gemini->draftReminder($business, $invoice)
            ?: $this->buildReminderMessage(
                $invoice, $daysLate, $balance, $invoice->currency, $business
            );

        return $this->whatsapp->sendText(
            $contact->whatsapp_number,
            $message,
            $business->whatsapp_phone_number_id,
            $business->whatsapp_access_token
        );
    }

    private function buildReminderMessage(
        Invoice  $invoice,
        int      $daysLate,
        string   $balance,
        string   $currency,
        Business $business
    ): string {
        $contactName  = $invoice->contact->name ?? 'Cher client';
        $businessName = $business->name;
        $invoiceNum   = $invoice->number;

        if ($daysLate === 0) {
            return "Bonjour {$contactName},\n\nNous vous rappelons que la facture *{$invoiceNum}* d'un montant de *{$balance} {$currency}* arrive à échéance aujourd'hui.\n\nMerci de procéder au règlement.\n\n— {$businessName}";
        }

        if ($daysLate <= 7) {
            return "Bonjour {$contactName},\n\nSauf erreur, la facture *{$invoiceNum}* ({$balance} {$currency}) est échue depuis {$daysLate} jour(s).\n\nPourriez-vous procéder au règlement ou nous contacter pour trouver une solution ?\n\n— {$businessName}";
        }

        return "Bonjour {$contactName},\n\n⚠️ Nous n'avons toujours pas reçu le paiement de la facture *{$invoiceNum}* ({$balance} {$currency}) due depuis *{$daysLate} jours*.\n\nMerci de régulariser cette situation rapidement ou de nous contacter.\n\n— {$businessName}";
    }
}
