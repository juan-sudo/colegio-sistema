<?php

namespace App\Exports;

use App\Models\Grade;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GradesReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private ?int $courseId = null, private ?int $periodId = null) {}

    public function query()
    {
        $query = Grade::with(['student', 'course', 'gradePeriod']);

        if ($this->courseId) {
            $query->where('course_id', $this->courseId);
        }
        if ($this->periodId) {
            $query->where('grade_period_id', $this->periodId);
        }

        return $query->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Curso', 'Periodo', 'Estudiante', 'Evaluación', 'Nota', 'Registrado por', 'Fecha'];
    }

    public function map($grade): array
    {
        return [
            $grade->course->name ?? '-',
            $grade->gradePeriod->name ?? '-',
            $grade->student->fullName() ?? '-',
            $grade->evaluation ?? '-',
            $grade->score,
            $grade->recorder->name ?? '-',
            $grade->created_at->format('d/m/Y'),
        ];
    }
}
