<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients_condominiums', function (Blueprint $table) {

            $table->id();

            $table->foreignId('client_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // id que vem da API
            $table->unsignedBigInteger('condominium_id');

            $table->timestamps();

            // garante que um condomínio só pertença a um cliente
            $table->unique('condominium_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients_condominiums');
    }
};