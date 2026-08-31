<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("assignments", function (Blueprint $table) {
            $table->id();
            $table->foreignId("course_id")->constrained()->cascadeOnDelete();
            $table->string("title");
            $table->text("description")->nullable();
            $table->string("file_path")->nullable();
            $table->dateTime("due_date")->nullable();
            $table->foreignId("created_by")->constrained("users")->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create("submissions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("assignment_id")->constrained()->cascadeOnDelete();
            $table->foreignId("student_id")->constrained()->cascadeOnDelete();
            $table->string("file_path");
            $table->dateTime("submitted_at");
            $table->decimal("grade", 5, 2)->nullable();
            $table->text("feedback")->nullable();
            $table->enum("status", ["entregado", "tarde", "calificado"])->default("entregado");
            $table->timestamps();

            $table->unique(["assignment_id", "student_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("submissions");
        Schema::dropIfExists("assignments");
    }
};
