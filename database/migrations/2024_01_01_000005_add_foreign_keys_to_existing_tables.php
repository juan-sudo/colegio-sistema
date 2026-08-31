<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grade_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_year_id')->nullable()->after('school_year');
            $table->unsignedBigInteger('shift_id')->nullable()->after('academic_year_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->after('teacher_id');
            $table->unsignedBigInteger('academic_year_id')->nullable()->after('subject_id');
        });

        Schema::table('grade_periods', function (Blueprint $table) {
            $table->unsignedBigInteger('school_phase_id')->nullable()->after('school_year');
        });

        Schema::table('grade_sections', function (Blueprint $table) {
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            $table->foreign('shift_id')->references('id')->on('shifts')->nullOnDelete();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subjects')->nullOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
        });

        Schema::table('grade_periods', function (Blueprint $table) {
            $table->foreign('school_phase_id')->references('id')->on('school_phases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grade_periods', function (Blueprint $table) {
            $table->dropForeign(['school_phase_id']);
            $table->dropColumn('school_phase_id');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['subject_id']);
            $table->dropColumn(['academic_year_id', 'subject_id']);
        });

        Schema::table('grade_sections', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'shift_id']);
        });
    }
};
