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
        Schema::create('role_model_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('model_id')->constrained('models');
            $table->foreignId('permission_id')->constrained('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_model_permissions');
    }
};
