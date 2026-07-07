<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope global — filtre automatiquement les données par business_id
 * de l'utilisateur connecté. Appliqué sur : Contact, Conversation,
 * Invoice, Quote, Service (tout sauf Business lui-même).
 */
class BusinessScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->business_id) {
            $builder->where($model->getTable() . '.business_id', auth()->user()->business_id);
        }
    }
}
