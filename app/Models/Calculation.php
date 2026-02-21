<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Calculation extends Model
{
    protected $fillable = [
        'period_start',
        'period_end',
        'status',
        'progress',
        'last_page',
        'total'
    ];

    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo(Client::class);
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
