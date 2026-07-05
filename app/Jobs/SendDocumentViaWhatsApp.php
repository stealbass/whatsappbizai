<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Quote;
use App\Services\DocumentService;
use App\Services\MarketingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDocumentViaWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    /**
     * @param string $type    'invoice' | 'quote'
     * @param int    $modelId ID de la facture ou du devis
     */
    public function __construct(
        private readonly string $type,
        private readonly int    $modelId
    ) {}

    public function handle(DocumentService $docs, MarketingService $marketing): void
    {
        if ($this->type === 'invoice') {
            $model = Invoice::with(['business', 'contact', 'items'])->findOrFail($this->modelId);

            // Génère le PDF si pas encore fait
            if (!$model->pdf_path) {
                $docs->generateInvoicePdf($model);
                $model->refresh();
            }

            $pdfUrl  = $docs->getPublicUrl($model->pdf_path);
            $caption = "Facture {$model->number} — {$model->total} {$model->currency}\nÉchéance : {$model->due_date->format('d/m/Y')}";
            $filename = "Facture-{$model->number}.pdf";

        } else {
            $model = Quote::with(['business', 'contact', 'items'])->findOrFail($this->modelId);

            if (!$model->pdf_path) {
                $docs->generateQuotePdf($model);
                $model->refresh();
            }

            $pdfUrl  = $docs->getPublicUrl($model->pdf_path);
            $caption = "Devis {$model->number} — {$model->total} {$model->currency}\nValable jusqu'au : {$model->valid_until->format('d/m/Y')}";
            $filename = "Devis-{$model->number}.pdf";
        }

        $sent = $marketing->sendPdfToContact(
            $model->business,
            $model->contact,
            $pdfUrl,
            $filename,
            $caption
        );

        if ($sent) {
            $model->update(['whatsapp_sent' => true, 'sent_at' => now()]);
            Log::info("Document envoyé via WhatsApp", [
                'type'   => $this->type,
                'number' => $model->number,
                'to'     => $model->contact->whatsapp_number,
            ]);
        } else {
            Log::error("Échec envoi WhatsApp document", [
                'type' => $this->type,
                'id'   => $this->modelId,
            ]);
            $this->fail(new \RuntimeException("Échec envoi WhatsApp"));
        }
    }
}
