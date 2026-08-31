<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('maximum_score', 5, 2)->default(100);
            $table->timestamps();
        });

        Schema::create('criterion_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_criterion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['assessment_criterion_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criterion_grades');
        Schema::dropIfExists('assessment_criteria');
    }
};
