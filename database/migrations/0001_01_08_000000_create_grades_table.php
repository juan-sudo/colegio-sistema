<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("grade_periods", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("school_year");
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
            $table->timestamps();
        });

        Schema::create("grades", function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->constrained()->cascadeOnDelete();
            $table->foreignId("course_id")->constrained()->cascadeOnDelete();
            $table->foreignId("grade_period_id")->constrained()->cascadeOnDelete();
            $table->decimal("score", 5, 2);
            $table->string("evaluation")->nullable();
            $table->text("comments")->nullable();
            $table->foreignId("recorded_by")->nullable()->constrained("users")->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("grades");
        Schema::dropIfExists("grade_periods");
    }
};
