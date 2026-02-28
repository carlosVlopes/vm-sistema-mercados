<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Sale extends Model
{
    protected $fillable = [
        'api_id',
        'user_id',
        'client_id',
        'condominium_id',
        'value',
        'sold_at',
        'payload'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    public static function upsertFromApi(array $sale, int $client_id, int $condominium_id, int $user_id): void
    {
        static::updateOrCreate(
            [
                'api_id' => $sale['id'],
                'client_id' => $client_id,
            ],
            [
                'user_id' => $user_id,
                'condominium_id' => $condominium_id,
                'value' => $sale['value'] ?? 0,
                'sold_at' => Carbon::parse($sale['occurred_at'])
                    ->timezone('America/Sao_Paulo'),
                'payload' => json_encode($sale),
            ]
        );
    }

    public static function upsertBulkFromApi(array $sales, int $client_id, int $condominium_id, int $user_id): void
    {
        $records = array_map(fn ($sale) => [
            'api_id' => $sale['id'],
            'client_id' => $client_id,
            'user_id' => $user_id,
            'condominium_id' => $condominium_id,
            'value' => $sale['value'] ?? 0,
            'sold_at' => Carbon::parse($sale['occurred_at'])->timezone('America/Sao_Paulo'),
            'payload' => json_encode($sale),
        ], $sales);

        foreach (array_chunk($records, 100) as $chunk) {
            static::upsert(
                $chunk,
                ['api_id', 'client_id'],
                ['user_id', 'condominium_id', 'value', 'sold_at', 'payload']
            );
        }
    }

    public function scopePeriod(Builder $query, int $client_id, int $condominium_id, string $period_start, string $period_end): Builder
    {
        return $query
            ->where('client_id', $client_id)
            ->where('condominium_id', $condominium_id)
            ->whereBetween('sold_at', [
                $period_start,
                $period_end
            ]);
    }
}
