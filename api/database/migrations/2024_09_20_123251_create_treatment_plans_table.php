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
            $table->enum('payment_method', ['cash', 'installments']);
            $table->enum('visit_type', ['in-person', 'online']);
            $table->integer('months_count')->default(0);
            $table->integer('checks_count')->default(0);
            $table->integer('deposit_amount')->default(0);
            $table->integer('total_amount')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->json('treatments_details');
            $table->string('desc')->nullable();
            $table->enum('status', ['invalid', 'valid', 'done'])->default('valid');
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
