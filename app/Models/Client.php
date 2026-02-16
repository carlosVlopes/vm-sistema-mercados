<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Client extends Authenticatable implements FilamentUser
{
    protected $fillable = [
        'name',
        'phonenumber',
        'email',
        'percentage',
        'receives_light',
        'register_token'
    ];

    public function condominiums()
    {
        return $this->belongsToMany(
            Condominium::class,
            'clients_condominiums',
            'client_id',
            'condominium_id'
        );
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    protected static function booted()
    {
        static::creating(function ($client) {
            if (Auth::check()) {
                $client->user_id = Auth::id();
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'sindico';
    }
}
