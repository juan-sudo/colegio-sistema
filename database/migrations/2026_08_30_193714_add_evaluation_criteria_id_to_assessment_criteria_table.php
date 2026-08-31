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
        Schema::table('assessment_criteria', function (Blueprint $table) {
            if (!Schema::hasColumn('assessment_criteria', 'evaluation_criteria_id')) {
                $table->unsignedBigInteger('evaluation_criteria_id')->nullable()->after('course_id');
            }
        });

        try {
            Schema::table('assessment_criteria', function (Blueprint $table) {
                $table->foreign('evaluation_criteria_id', 'assessment_criteria_evaluation_criteria_id_foreign')
                    ->references('id')->on('evaluation_criteria')->cascadeOnDelete();
            });
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $sql = 'ALTER TABLE assessment_criteria DROP FOREIGN KEY assessment_criteria_evaluation_criteria_id_foreign';
                \Illuminate\Support\Facades\DB::statement($sql);

                Schema::table('assessment_criteria', function (Blueprint $table) {
                    $table->foreign('evaluation_criteria_id', 'assessment_criteria_evaluation_criteria_id_foreign')
                        ->references('id')->on('evaluation_criteria')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_criteria', function (Blueprint $table) {
            $table->dropForeign('assessment_criteria_evaluation_criteria_id_foreign');
            $table->dropColumn('evaluation_criteria_id');
        });
    }
};
