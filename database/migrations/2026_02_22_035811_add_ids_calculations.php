<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->cascadeOnDelete()->after('user_id');
            $table->foreignId('condominium_id')->constrained('clients_condominiums')->cascadeOnDelete()->after('client_id');
            $table->string('condominium_name')->nullable()->index()->after('condominium_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calculations', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'condominium_id', 'condominium_name']);
        });
    }
};
