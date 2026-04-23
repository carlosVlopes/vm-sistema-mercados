<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->timestamp('register_token_expires_at')->nullable()->after('register_token');
        });

        // The `register_token` column is longText, so a plain index is not allowed
        // on MySQL. Use a prefix index on the first 64 bytes instead.
        DB::statement('CREATE INDEX clients_register_token_idx ON clients (register_token(64))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX clients_register_token_idx ON clients');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('register_token_expires_at');
        });
    }
};
