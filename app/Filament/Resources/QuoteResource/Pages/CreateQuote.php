<?php

namespace App\Filament\Resources\QuoteResource\Pages;

use App\Filament\Resources\QuoteResource;
use App\Services\DocumentService;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user     = auth()->user();
        $business = $user->business;

        $data['business_id'] = $business->id;

        if (empty($data['number'])) {
            $docs = app(DocumentService::class);
            $data['number'] = $docs->nextQuoteNumber($business);
        }

        $subtotal = collect($data['items'] ?? [])->sum('total');
        $tax      = round($subtotal * (($data['tax_rate'] ?? 0) / 100), 2);
        $discount = $data['discount'] ?? 0;

        $data['subtotal']   = $subtotal;
        $data['tax_amount'] = $tax;
        $data['total']      = $subtotal + $tax - $discount;

        return $data;
    }
}
