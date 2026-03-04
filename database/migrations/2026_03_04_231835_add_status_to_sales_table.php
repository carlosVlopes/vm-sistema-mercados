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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 20)->default('OK')->after('value')->index();
        });

        // Atualizar registros existentes com o status do payload
        DB::statement("
            UPDATE sales
            SET status = JSON_UNQUOTE(JSON_EXTRACT(payload, '$.status'))
            WHERE payload IS NOT NULL
              AND JSON_EXTRACT(payload, '$.status') IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
