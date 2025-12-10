<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_code'); // must match Badge.code
            $table->enum('status', ['locked', 'earned', 'redeemed'])->default('earned');
            $table->timestamps();

            $table->unique(['user_id', 'badge_code']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
