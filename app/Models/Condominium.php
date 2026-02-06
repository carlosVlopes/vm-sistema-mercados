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
            'id',
            'client_id'
        );
    }
}
