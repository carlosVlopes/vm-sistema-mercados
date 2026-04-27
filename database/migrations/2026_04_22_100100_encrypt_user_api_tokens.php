<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->orderBy('id')
            ->each(function ($row) {
                // skip if it already looks encrypted (starts with Laravel's payload prefix)
                try {
                    Crypt::decryptString($row->api_token);
                    return; // already encrypted
                } catch (\Throwable $e) {
                    DB::table('users')
                        ->where('id', $row->id)
                        ->update(['api_token' => Crypt::encryptString($row->api_token)]);
                }
            });
    }

    public function down(): void
    {
        DB::table('users')
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->orderBy('id')
            ->each(function ($row) {
                try {
                    $plain = Crypt::decryptString($row->api_token);
                    DB::table('users')
                        ->where('id', $row->id)
                        ->update(['api_token' => $plain]);
                } catch (\Throwable $e) {
                    // already plaintext
                }
            });
    }
};
