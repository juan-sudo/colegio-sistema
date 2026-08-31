<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("students", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("grade_section_id")->nullable()->constrained()->nullOnDelete();
            $table->string("code")->unique();
            $table->string("qr_token")->unique();
            $table->string("barcode")->unique();
            $table->string("first_name");
            $table->string("last_name");
            $table->date("birth_date")->nullable();
            $table->string("photo")->nullable();
            $table->string("biometric_id")->nullable()->unique();
            $table->boolean("active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("students");
    }
};
