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
        Schema::create('treatment_plan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('treatment_plans');
            $table->foreignId('treatment_id')->constrained('treatments');
            $table->enum('jaw', ['upper', 'lower']);
            $table->integer('tooth_number');
            $table->string('desc');
            $table->enum('status', ['pending', 'done']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_plan_details');
    }
};
