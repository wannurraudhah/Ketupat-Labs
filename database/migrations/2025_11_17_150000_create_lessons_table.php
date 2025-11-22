<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('topic');
                $table->foreignId('teacher_id');
                $table->foreignId('classroom_id');
                $table->integer('duration')->nullable();
                $table->string('material_path')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();

                $table->index('teacher_id');
                $table->index('classroom_id');
                $table->index('is_published');

                $table->foreign('teacher_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

                $table->foreign('classroom_id')
                    ->references('id')
                    ->on('classrooms')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};

