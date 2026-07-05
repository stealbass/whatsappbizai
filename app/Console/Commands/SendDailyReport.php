<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyReport extends Command
{
    protected $signature   = 'report:daily';
    protected $description = 'Envoie le rapport quotidien WhatsApp au propriétaire de chaque business';

    public function handle(WhatsAppService $whatsapp): int
    {
        $businesses = Business::where('is_active', true)
            ->whereNotNull('whatsapp_phone_number_id')
            ->whereNotNull('whatsapp_access_token')
            ->whereNotNull('phone')
            ->get();

        foreach ($businesses as $business) {
            try {
                $report = $this->buildReport($business);

                $sent = $whatsapp->sendText(
                    $business->phone,
                    $report,
                    $business->whatsapp_phone_number_id,
                    $business->whatsapp_access_token
                );

                if ($sent) {
                    $this->info("✅ Rapport envoyé à {$business->name}");
                } else {
                    $this->error("❌ Échec rapport {$business->name}");
                }

            } catch (\Throwable $e) {
                Log::error("SendDailyReport error", ['business' => $business->id, 'error' => $e->getMessage()]);
                $this->error("Exception: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }

    private function buildReport(Business $business): string
    {
        $today     = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        // Messages reçus hier
        $messagesYesterday = Message::whereHas('conversation', fn($q) =>
                $q->where('business_id', $business->id)
            )
            ->where('direction', 'inbound')
            ->whereBetween('created_at', [$yesterday, $today])
            ->count();

        // Réponses IA hier
        $aiReplies = Message::whereHas('conversation', fn($q) =>
                $q->where('business_id', $business->id)
            )
            ->where('is_ai', true)
            ->whereBetween('created_at', [$yesterday, $today])
            ->count();

        // Nouveaux contacts hier
        $newContacts = Contact::where('business_id', $business->id)
            ->whereBetween('created_at', [$yesterday, $today])
            ->count();

        // Conversations ouvertes
        $openConvs = Conversation::where('business_id', $business->id)
            ->where('status', 'open')
            ->count();

        // Factures en retard
        $overdueInvoices = Invoice::where('business_id', $business->id)
            ->where('status', 'sent')
            ->where('due_date', '<', now())
            ->count();

        // Factures payées hier
        $paidYesterday = Invoice::where('business_id', $business->id)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$yesterday, $today])
            ->sum('total');

        $date     = now()->subDay()->format('d/m/Y');
        $currency = $business->currency;
        $paid     = number_format($paidYesterday, 0, ',', ' ');

        $overdueAlert = $overdueInvoices > 0
            ? "\n⚠️ {$overdueInvoices} facture(s) en retard de paiement"
            : "\n✅ Aucune facture en retard";

        return <<<MSG
📊 *Rapport WhatsAppBizAI — {$date}*

*{$business->name}*

📩 Messages reçus : *{$messagesYesterday}*
🤖 Réponses IA : *{$aiReplies}*
👤 Nouveaux contacts : *{$newContacts}*
💬 Conversations ouvertes : *{$openConvs}*
💰 Encaissé hier : *{$paid} {$currency}*{$overdueAlert}

_Gérez votre activité sur https://whatsappbizai.com/admin_
MSG;
    }
}
