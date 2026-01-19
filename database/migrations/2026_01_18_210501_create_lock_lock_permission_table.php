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
        Schema::create('lock_lock_permission', function (Blueprint $table) {
            $table->foreignId('lock_permission_id')->constrained('lock_permissions')->onDelete('cascade');
            $table->foreignId('lock_id')->constrained('locks')->onDelete('cascade');

            $table->primary(['lock_permission_id', 'lock_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lock_lock_permission');
    }
};
