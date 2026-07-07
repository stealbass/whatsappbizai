<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Services\DocumentService;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $subtotal = collect($data['items'] ?? [])->sum('total');
        $tax      = round($subtotal * (($data['tax_rate'] ?? 0) / 100), 2);
        $discount = $data['discount'] ?? 0;

        $data['subtotal']   = $subtotal;
        $data['tax_amount'] = $tax;
        $data['total']      = $subtotal + $tax - $discount;

        return $data;
    }
}
