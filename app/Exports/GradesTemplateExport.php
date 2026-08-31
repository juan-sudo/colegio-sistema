<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GradesTemplateExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $students) {}

    public function headings(): array
    {
        return ["codigo_alumno", "nombres", "apellidos", "nota", "evaluacion"];
    }

    public function collection()
    {
        return $this->students->map(fn ($s) => [
            "codigo_alumno" => $s->code,
            "nombres" => $s->first_name,
            "apellidos" => $s->last_name,
            "nota" => "",
            "evaluacion" => "",
        ]);
    }
}
