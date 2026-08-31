<?php
namespace App\Imports;

use App\Models\Grade;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Formato esperado del Excel (columnas, con encabezados):
 * | codigo_alumno | nombres | apellidos | nota | evaluacion |
 *
 * "codigo_alumno" debe coincidir con el campo "code" de la tabla students.
 */
class GradesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function __construct(
        private int $courseId,
        private int $gradePeriodId,
        private int $recordedBy
    ) {}

    public function model(array $row)
    {
        $student = Student::where("code", $row["codigo_alumno"])->first();
        if (!$student) {
            return null; // fila ignorada si el código no existe
        }

        return Grade::updateOrCreate(
            [
                "student_id" => $student->id,
                "course_id" => $this->courseId,
                "grade_period_id" => $this->gradePeriodId,
                "evaluation" => $row["evaluacion"] ?? "General",
            ],
            [
                "score" => $row["nota"],
                "recorded_by" => $this->recordedBy,
            ]
        );
    }

    public function rules(): array
    {
        return [
            "codigo_alumno" => "required",
            "nota" => "required|numeric|min:0|max:20",
        ];
    }
}
