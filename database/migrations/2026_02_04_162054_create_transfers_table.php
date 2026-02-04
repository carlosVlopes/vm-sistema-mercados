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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->timestamp('date')->useCurrent();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->integer('total_value');
            $table->integer('sales_value');
            $table->integer('light_value');
            $table->string('proof_payment');
            $table->string('proof_light');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
