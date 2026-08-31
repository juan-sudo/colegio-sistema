<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private ?int $courseId = null, private ?string $dateFrom = null, private ?string $dateTo = null) {}

    public function query()
    {
        $query = Attendance::with(['student', 'course']);

        if ($this->courseId) {
            $query->where('course_id', $this->courseId);
        }
        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        return $query->orderByDesc('date');
    }

    public function headings(): array
    {
        return ['Fecha', 'Curso', 'Estudiante', 'Estado', 'Hora entrada', 'Método'];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->course->name ?? '-',
            $attendance->student->fullName() ?? '-',
            ucfirst($attendance->status),
            $attendance->time_in ?? '-',
            ucfirst($attendance->method),
        ];
    }
}
