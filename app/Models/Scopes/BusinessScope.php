<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BusinessScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        // Super-admins bypass BusinessScope — they see everything
        if ($user->is_super_admin) {
            return;
        }

        if ($user->business_id) {
            $builder->where($model->getTable() . '.business_id', $user->business_id);
        }
    }
}
