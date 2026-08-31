<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("courses", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("grade_section_id")->constrained()->cascadeOnDelete();
            $table->foreignId("teacher_id")->nullable()->constrained()->nullOnDelete();
            $table->string("school_year");
            $table->timestamps();
        });

        Schema::create("course_student", function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->constrained()->cascadeOnDelete();
            $table->foreignId("student_id")->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("course_student");
        Schema::dropIfExists("courses");
    }
};
