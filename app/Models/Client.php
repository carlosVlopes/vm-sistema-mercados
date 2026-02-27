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
        'password',
        'percentage',
        'receives_light',
        'register_token'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'condominiums_ids' => 'array', // Evita o erro de conversão
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

    public function calculations()
    {
        return $this->hasMany(Calculation::class);
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
