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
        Schema::create('trees', function (Blueprint $table) {
            $table->id();
            $table->foreingId('user_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->foreingId('seed_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->unsignedInteger('level')->default(0);
            $table->unsignedInteger('progress')->default(0);
            $table->unsignedInteger('health')->default(100);
            $table->string('status', 20)->default('active');
            $table->timestamps('last_cared_at')->nullable();
            $table->timestamps('next_care_at')->nullable();
            $table->timestamps('last_decay_at')->nullable();
            $table->timestamps('harvest_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
