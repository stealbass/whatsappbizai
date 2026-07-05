<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendOverdueReminders extends Command
{
    protected $signature   = 'reminders:send-overdue';
    protected $description = 'Envoie les relances WhatsApp pour les factures échues';

    public function handle(ReminderService $reminder): int
    {
        $businesses = Business::where('is_active', true)->get();
        $total      = 0;

        foreach ($businesses as $business) {
            if (!$business->whatsapp_phone_number_id || !$business->whatsapp_access_token) {
                continue;
            }

            $sent  = $reminder->sendOverdueReminders($business);
            $total += $sent;

            $this->info("[{$business->name}] {$sent} relance(s) envoyée(s)");
        }

        $this->info("Total : {$total} relance(s) envoyée(s)");
        return self::SUCCESS;
    }
}
