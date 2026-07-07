<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'business_id',
        'role',
        'is_super_admin',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'super-admin') {
            return $this->is_super_admin;
        }

        return $this->role === 'admin' && $this->is_active;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
