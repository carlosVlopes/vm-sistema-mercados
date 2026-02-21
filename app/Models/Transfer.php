<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transfer extends Model
{
    protected $fillable = [
        'client_id',
        'condominium_name',
        'date',
        'period_start',
        'period_end',
        'transfer_value',
        'gross_total',
        'light_value',
        'proof_payment',
        'proof_light',
        'user_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (Auth::check()) {
                $user->user_id = Auth::id();
            }
        });
    }
}
