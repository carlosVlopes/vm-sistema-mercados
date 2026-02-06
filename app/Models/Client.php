<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
            'clients_condominiums',   // tabela pivot
            'client_id',      // FK deste model
            'id'              // FK do outro
        );
    }

    protected static function booted()
    {
        static::creating(function ($client) {
            if (auth()->check()) {
                $client->user_id = auth()->id();
            }
        });
    }

    public static function get_condominiums(): array
    {
        return Cache::remember('condominiums_api', 600, function () {

            $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients', [
                'access_token' => env('VM_API_TOKEN'),
            ]);

            $result = $response->json();

            return collect($result)->pluck('name', 'id')->toArray();
        });
    }
}
