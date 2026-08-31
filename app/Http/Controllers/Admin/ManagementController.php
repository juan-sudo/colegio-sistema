<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AssessmentCriterion, Enrollment, EvaluationPeriod, FinancialTransaction, Payment, PaymentConcept, SchoolPhase, SchoolSetting, SchoolYear, Schedule, Shift, Student, Teacher, TeacherAssignment, Course, GradeSection};
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManagementReportExport;

class ManagementController extends Controller
{
    public function index()
    {
        return view('admin.management.index', [
            'years' => SchoolYear::latest('starts_at')->get(), 'shifts' => Shift::orderBy('starts_at')->get(),
            'phases' => SchoolPhase::with('schoolYear')->latest()->get(), 'periods' => EvaluationPeriod::with('schoolYear')->latest()->get(),
            'enrollments' => Enrollment::with(['student','schoolYear','section'])->latest()->paginate(12, ['*'], 'enrollments'),
            'assignments' => TeacherAssignment::with(['teacher','course','section','schedules'])->latest()->get(),
            'payments' => Payment::with(['student','concept'])->latest()->paginate(12, ['*'], 'payments'),
            'concepts' => PaymentConcept::where('active', true)->get(), 'transactions' => FinancialTransaction::latest('transaction_date')->paginate(12, ['*'], 'transactions'),
            'criteria' => AssessmentCriterion::with('course')->latest()->get(), 'settings' => SchoolSetting::orderBy('group')->get()->keyBy('key'),
            'students' => Student::orderBy('last_name')->get(), 'teachers' => Teacher::orderBy('last_name')->get(), 'courses' => Course::orderBy('name')->get(), 'sections' => GradeSection::orderBy('name')->get(),
            'summary' => ['students' => Student::count(), 'enrollments' => Enrollment::where('status','active')->count(), 'pending' => Payment::whereIn('status',['pending','overdue'])->sum('amount'), 'income' => FinancialTransaction::where('type','income')->sum('amount'), 'expenses' => FinancialTransaction::whereIn('type',['expense','fixed_expense'])->sum('amount')],
        ]);
    }

    public function store(Request $request, string $entity)
    {
        $data = match ($entity) {
            'years' => $request->validate(['name'=>'required|string|max:30|unique:school_years,name','starts_at'=>'required|date','ends_at'=>'required|date|after:starts_at']),
            'shifts' => $request->validate(['name'=>'required|string|max:50|unique:shifts,name','starts_at'=>'required','ends_at'=>'required|after:starts_at']),
            'phases' => $request->validate(['school_year_id'=>'required|exists:school_years,id','name'=>'required|string|max:80','starts_at'=>'required|date','ends_at'=>'required|date|after:starts_at']),
            'periods' => $request->validate(['school_year_id'=>'required|exists:school_years,id','name'=>'required|string|max:80','code'=>'nullable|string|max:20','weight'=>'required|numeric|min:0|max:100','starts_at'=>'nullable|date','ends_at'=>'nullable|date']),
            'enrollments' => $request->validate(['school_year_id'=>'required|exists:school_years,id','student_id'=>'required|exists:students,id','grade_section_id'=>'required|exists:grade_sections,id','shift_id'=>'nullable|exists:shifts,id','enrolled_at'=>'required|date','enrollment_fee'=>'nullable|numeric|min:0']),
            'assignments' => $request->validate(['school_year_id'=>'required|exists:school_years,id','teacher_id'=>'required|exists:teachers,id','course_id'=>'required|exists:courses,id','grade_section_id'=>'required|exists:grade_sections,id','weekly_hours'=>'required|integer|min:1|max:50']),
            'schedules' => $request->validate(['teacher_assignment_id'=>'required|exists:teacher_assignments,id','weekday'=>'required|integer|min:1|max:7','starts_at'=>'required','ends_at'=>'required|after:starts_at','room'=>'nullable|string|max:30']),
            'concepts' => $request->validate(['school_year_id'=>'nullable|exists:school_years,id','name'=>'required|string|max:100','type'=>'required|in:enrollment,tuition,other','amount'=>'required|numeric|min:0','due_day'=>'nullable|integer|min:1|max:31']),
            'payments' => $request->validate(['student_id'=>'required|exists:students,id','payment_concept_id'=>'required|exists:payment_concepts,id','enrollment_id'=>'nullable|exists:enrollments,id','voucher_number'=>'nullable|string|max:50','amount'=>'required|numeric|min:0','due_date'=>'nullable|date','paid_at'=>'nullable|date','status'=>'required|in:pending,paid,overdue,cancelled']),
            'transactions' => $request->validate(['type'=>'required|in:income,expense,fixed_expense','category'=>'required|string|max:80','description'=>'required|string|max:255','amount'=>'required|numeric|min:0','transaction_date'=>'required|date']),
            'criteria' => $request->validate(['course_id'=>'required|exists:courses,id','evaluation_period_id'=>'nullable|exists:evaluation_periods,id','name'=>'required|string|max:100','weight'=>'required|numeric|min:0|max:100','maximum_score'=>'required|numeric|min:1|max:100']),
            'settings' => $request->validate(['key'=>'required|string|max:80','value'=>'nullable|string|max:2000','group'=>'required|string|max:50']),
            default => abort(404),
        };
        if ($entity === 'years') $data['status'] = 'open';
        if (in_array($entity, ['shifts','phases','concepts'])) $data['active'] = true;
        if ($entity === 'enrollments') { $data['code'] = 'MAT-'.now()->format('Y').'-'.str_pad((string)(Enrollment::count()+1), 5, '0', STR_PAD_LEFT); $data['status']='active'; }
        $model = match ($entity) { 'years'=>SchoolYear::class,'shifts'=>Shift::class,'phases'=>SchoolPhase::class,'periods'=>EvaluationPeriod::class,'enrollments'=>Enrollment::class,'assignments'=>TeacherAssignment::class,'schedules'=>Schedule::class,'concepts'=>PaymentConcept::class,'payments'=>Payment::class,'transactions'=>FinancialTransaction::class,'criteria'=>AssessmentCriterion::class,'settings'=>SchoolSetting::class };
        if ($entity === 'settings') SchoolSetting::updateOrCreate(['key'=>$data['key']], $data); else $record = $model::create($data);
        if ($entity === 'payments' && $data['status'] === 'paid') FinancialTransaction::create(['type'=>'income','category'=>'Cobranza','description'=>'Pago registrado: '.($data['voucher_number'] ?: $record->id),'amount'=>$data['amount'],'transaction_date'=>$data['paid_at'] ?? now()->toDateString(),'payment_id'=>$record->id,'recorded_by'=>$request->user()->id]);
        return back()->with('success', 'Registro guardado correctamente.');
    }

    public function destroy(string $entity, int $id)
    {
        $model = match ($entity) { 'years'=>SchoolYear::class,'shifts'=>Shift::class,'phases'=>SchoolPhase::class,'periods'=>EvaluationPeriod::class,'enrollments'=>Enrollment::class,'assignments'=>TeacherAssignment::class,'schedules'=>Schedule::class,'concepts'=>PaymentConcept::class,'payments'=>Payment::class,'transactions'=>FinancialTransaction::class,'criteria'=>AssessmentCriterion::class,'settings'=>SchoolSetting::class, default=>abort(404) };
        $model::findOrFail($id)->delete(); return back()->with('success','Registro eliminado.');
    }
    public function closeYear(SchoolYear $schoolYear) { $schoolYear->update(['status'=>'closed']); return back()->with('success','Año escolar cerrado.'); }
    public function export(string $report) { return Excel::download(new ManagementReportExport($report), "reporte-{$report}.xlsx"); }
}
