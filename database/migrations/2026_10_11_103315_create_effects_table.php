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
        Schema::create('effects', function (Blueprint $table) {
             $table->id();
            $table->foreignId('shop_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tree_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->timestamp('started_time')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effects');
    }
};
