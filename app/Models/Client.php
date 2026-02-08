<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phonenumber',
        'email',
        'percentage',
        'receives_light',
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
}
