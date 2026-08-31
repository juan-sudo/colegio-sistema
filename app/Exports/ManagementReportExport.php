<?php
namespace App\Exports;
use App\Models\{Enrollment, FinancialTransaction, Payment, Grade, Attendance};
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings};
class ManagementReportExport implements FromCollection, WithHeadings {
    public function __construct(private string $report) {}
    public function headings(): array { return match($this->report) { 'payments'=>['Alumno','Concepto','Monto','Vencimiento','Pago','Estado'], 'finance'=>['Tipo','Categoría','Descripción','Monto','Fecha'], 'attendance'=>['Alumno','Curso','Fecha','Estado','Método'], default=>['Alumno','Curso','Periodo','Evaluación','Nota'] }; }
    public function collection() { return match($this->report) {
        'payments'=>Payment::with(['student','concept'])->get()->map(fn($x)=>[$x->student->fullName(),$x->concept->name,$x->amount,$x->due_date?->format('Y-m-d'),$x->paid_at?->format('Y-m-d'),$x->status]),
        'finance'=>FinancialTransaction::orderByDesc('transaction_date')->get()->map(fn($x)=>[$x->type,$x->category,$x->description,$x->amount,$x->transaction_date->format('Y-m-d')]),
        'attendance'=>Attendance::with(['student','course'])->get()->map(fn($x)=>[$x->student->fullName(),$x->course->name,$x->date->format('Y-m-d'),$x->status,$x->method]),
        default=>Grade::with(['student','course','gradePeriod'])->get()->map(fn($x)=>[$x->student->fullName(),$x->course->name,$x->gradePeriod->name,$x->evaluation,$x->score]),
    }; }
}
