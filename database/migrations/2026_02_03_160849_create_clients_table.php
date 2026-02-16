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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->index();
            $table->string('name')->nullable()->index();
            $table->string('phonenumber')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('password')->nullable();
            $table->decimal('percentage', 5, 2)->nullable()->index();
            $table->integer('receives_light')->nullable()->index();
            $table->longText('register_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
