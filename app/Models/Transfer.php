<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transfer extends Model
{
    protected $casts = [
        'proof_files' => 'array',
    ];

    protected $fillable = [
        'client_id',
        'condominium_name',
        'date',
        'period_start',
        'period_end',
        'transfer_value',
        'gross_total',
        'net_total',
        'light_value',
        'proof_files',
        'user_id',
        'notes'
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
