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
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient')->constrained('patients');
            $table->enum('payment_type', ['cash', 'installments']);
            $table->integer('months')->default(0);
            $table->integer('deposit')->default(0);
            $table->string('desc');
            $table->enum('status', ['editing','sent', 'done'])->default('editing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plans');
    }
};
