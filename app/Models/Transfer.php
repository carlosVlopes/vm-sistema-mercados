<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'client_id',
        'condominium_id',
        'date',
        'period_start',
        'period_end',
        'total_value',
        'sales_value',
        'light_value',
        'proof_payment',
        'proof_light'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }
}
