<?php
namespace Database\Seeders;
use App\Models\{Course, Enrollment, EvaluationPeriod, PaymentConcept, SchoolPhase, SchoolYear, Shift, Student, TeacherAssignment};
use Illuminate\Database\Seeder;

class AcademicManagementSeeder extends Seeder
{
    public function run(): void
    {
        $year = SchoolYear::firstOrCreate(['name'=>'2026'], ['starts_at'=>'2026-03-01','ends_at'=>'2026-12-20','status'=>'open']);
        $shift = Shift::firstOrCreate(['name'=>'Mañana'], ['starts_at'=>'07:30','ends_at'=>'13:30','active'=>true]);
        SchoolPhase::firstOrCreate(['school_year_id'=>$year->id,'name'=>'Primer semestre'], ['starts_at'=>'2026-03-01','ends_at'=>'2026-07-31','active'=>true]);
        $period = EvaluationPeriod::firstOrCreate(['school_year_id'=>$year->id,'name'=>'Bimestre 1'], ['code'=>'B1','weight'=>100,'starts_at'=>'2026-03-01','ends_at'=>'2026-04-30']);
        PaymentConcept::firstOrCreate(['school_year_id'=>$year->id,'name'=>'Matrícula 2026'], ['type'=>'enrollment','amount'=>250,'due_day'=>null,'active'=>true]);
        PaymentConcept::firstOrCreate(['school_year_id'=>$year->id,'name'=>'Pensión mensual'], ['type'=>'tuition','amount'=>350,'due_day'=>5,'active'=>true]);
        $student = Student::first(); $course = Course::first();
        if ($student) Enrollment::firstOrCreate(['school_year_id'=>$year->id,'student_id'=>$student->id], ['grade_section_id'=>$student->grade_section_id,'shift_id'=>$shift->id,'code'=>'MAT-2026-00001','enrolled_at'=>'2026-03-01','enrollment_fee'=>250,'status'=>'active']);
        if ($course && $course->teacher_id) TeacherAssignment::firstOrCreate(['school_year_id'=>$year->id,'teacher_id'=>$course->teacher_id,'course_id'=>$course->id,'grade_section_id'=>$course->grade_section_id], ['weekly_hours'=>4]);
        $this->command->info('Módulo académico y financiero inicializado.');
    }
}
