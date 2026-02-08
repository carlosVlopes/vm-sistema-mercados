<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condominium extends Model
{
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'clients_condominiums',
            'condominium_id',
            'client_id'
        );
    }
}
