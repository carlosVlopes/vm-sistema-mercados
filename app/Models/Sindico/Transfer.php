<?php

namespace App\Models\Sindico;

use App\Models\Client;
use App\Models\Condominium;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $casts = [
        'proof_files' => 'array',
    ];

    protected $fillable = [
        'client_id',
        'condominium_id',
        'date',
        'period_start',
        'period_end',
        'transfer_value',
        'gross_total',
        'light_value',
        'proof_files',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }
}
