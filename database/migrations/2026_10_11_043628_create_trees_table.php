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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seed_id')->constrained()->cascadeOnDelete();
            // Reglas del juego: todo árbol inicia en nivel 0, progreso 0, salud 100 y ACTIVE.
            $table->integer('tree_level')->default(0);
            $table->integer('progress')->default(0);
            $table->integer('health')->default(100);
            $table->enum('status', ['active', 'mature', 'dead', 'harvested'])->default('active');
            $table->timestamp('last_care_at')->nullable();
            $table->timestamp('next_care_at')->nullable();
            $table->timestamp('last_decay_at')->nullable();
            $table->timestamp('harvested_at')->nullable();
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
