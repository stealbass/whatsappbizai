<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Services\DocumentService;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    /**
     * Injecte automatiquement le business_id et génère le numéro de facture
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user     = auth()->user();
        $business = $user->business;

        $data['business_id'] = $business->id;

        // Auto-génère le numéro si vide
        if (empty($data['number'])) {
            $docs = app(DocumentService::class);
            $data['number'] = $docs->nextInvoiceNumber($business);
        }

        // Calcule les totaux
        $subtotal = collect($data['items'] ?? [])->sum('total');
        $tax      = round($subtotal * (($data['tax_rate'] ?? 0) / 100), 2);
        $discount = $data['discount'] ?? 0;

        $data['subtotal']   = $subtotal;
        $data['tax_amount'] = $tax;
        $data['total']      = $subtotal + $tax - $discount;
        $data['paid_amount'] = 0;

        return $data;
    }
}
