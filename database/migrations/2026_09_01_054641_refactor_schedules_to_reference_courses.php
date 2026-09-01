<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No production data yet; schedules previously had no link to courses,
        // so there is nothing meaningful to backfill into the new column.
        DB::table('schedules')->truncate();

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['grade_section_id']);
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['grade_section_id', 'subject_id', 'teacher_id']);
            $table->foreignId('course_id')->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::dropIfExists('grade_section_subject');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
            $table->foreignId('grade_section_id')->after('id')->constrained('grade_sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
        });

        Schema::create('grade_section_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_section_id')->constrained('grade_sections')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->integer('hours_per_week')->nullable();
            $table->timestamps();

            $table->unique(['grade_section_id', 'subject_id']);
        });
    }
};
