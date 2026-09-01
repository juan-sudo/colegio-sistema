<?php
namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AccountingEntry;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\EvaluationCriteria;
use App\Models\GradePeriod;
use App\Models\GradeSection;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\SchoolPhase;
use App\Models\SchoolSchedule;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@colegio.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $academicYear = AcademicYear::create([
            'name' => '2026',
            'start_date' => '2026-03-01',
            'end_date' => '2026-12-15',
            'is_current' => true,
        ]);

        $morning = Shift::create(['name' => 'Mañana', 'start_time' => '07:00', 'end_time' => '13:00']);
        $afternoon = Shift::create(['name' => 'Tarde', 'start_time' => '14:00', 'end_time' => '19:00']);

        $phase1 = SchoolPhase::create(['name' => 'Bimestre 1', 'order' => 1]);
        $phase2 = SchoolPhase::create(['name' => 'Bimestre 2', 'order' => 2]);

        $section = GradeSection::create([
            'name' => '3ro A',
            'level' => 'Secundaria',
            'school_year' => '2026',
            'academic_year_id' => $academicYear->id,
            'shift_id' => $morning->id,
        ]);

        $math = Subject::create(['name' => 'Matemática', 'code' => 'MAT']);
        $spanish = Subject::create(['name' => 'Comunicación', 'code' => 'COM']);
        $english = Subject::create(['name' => 'Inglés', 'code' => 'ING']);

        $teacherUser = User::create([
            'name' => 'Prof. Carlos Ramírez',
            'email' => 'profesor@colegio.test',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);
        $teacher = Teacher::create([
            'user_id' => $teacherUser->id,
            'code' => 'DOC-001',
            'first_name' => 'Carlos',
            'last_name' => 'Ramírez',
            'specialty' => 'Matemática',
        ]);

        $course = Course::create([
            'name' => 'Matemática',
            'grade_section_id' => $section->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $math->id,
            'academic_year_id' => $academicYear->id,
            'school_year' => '2026',
        ]);

        Schedule::create([
            'course_id' => $course->id,
            'shift_id' => $morning->id,
            'day_of_week' => 'Lunes',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'classroom' => 'Aula 301',
        ]);

        GradePeriod::create([
            'name' => 'Bimestre 1',
            'school_year' => '2026',
            'school_phase_id' => $phase1->id,
            'start_date' => '2026-03-01',
            'end_date' => '2026-04-30',
        ]);

        $parentUser = User::create([
            'name' => 'María López',
            'email' => 'padre@colegio.test',
            'password' => Hash::make('password'),
            'role' => 'parent',
        ]);
        $guardian = Guardian::create([
            'user_id' => $parentUser->id,
            'first_name' => 'María',
            'last_name' => 'López',
            'phone_whatsapp' => '51987654321',
        ]);

        $studentUser = User::create([
            'name' => 'Juan López',
            'email' => 'estudiante@colegio.test',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'grade_section_id' => $section->id,
            'dni' => '12345678',
            'code' => now()->year . '-12345678',
            'qr_token' => Str::uuid(),
            'barcode' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'López',
            'birth_date' => '2011-05-14',
            'biometric_id' => '12345678',
        ]);

        $guardian->students()->attach($student->id, ['relationship' => 'Madre']);
        $course->students()->attach($student->id);

        Enrollment::create([
            'student_id' => $student->id,
            'grade_section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'matriculado',
            'enrollment_date' => '2026-02-15',
        ]);

        Payment::create([
            'student_id' => $student->id,
            'type' => 'matricula',
            'amount' => 500.00,
            'discount' => 0,
            'paid' => 500.00,
            'status' => 'pagado',
            'due_date' => '2026-02-28',
            'paid_date' => '2026-02-15',
            'payment_method' => 'efectivo',
        ]);

        Payment::create([
            'student_id' => $student->id,
            'type' => 'pension',
            'amount' => 300.00,
            'discount' => 0,
            'paid' => 0,
            'status' => 'pendiente',
            'due_date' => '2026-03-05',
        ]);

        AccountingEntry::create([
            'type' => 'ingreso',
            'category' => 'Matrículas',
            'description' => 'Matrícula Juan López',
            'amount' => 500.00,
            'date' => '2026-02-15',
        ]);

        AccountingEntry::create([
            'type' => 'gasto_fijo',
            'category' => 'Servicios',
            'description' => 'Luz febrero',
            'amount' => 1200.00,
            'date' => '2026-02-01',
        ]);

        Setting::set('school_name', 'Colegio Ejemplo', 'string', 'general');
        Setting::set('school_address', 'Av. Principal 123', 'string', 'general');
        Setting::set('school_phone', '01-234-5678', 'string', 'general');
        Setting::set('attendance_method', 'qr', 'string', 'general');
        Setting::set('currency', 'PEN', 'string', 'financial');
        Setting::set('matricula_amount', '500.00', 'number', 'financial');
        Setting::set('pension_amount', '300.00', 'number', 'financial');
        Setting::set('late_fee_percentage', '5', 'number', 'financial');

        if (! SchoolSchedule::query()->exists()) {
            SchoolSchedule::create([
                'name' => 'Turno mañana',
                'entry_window_start' => '07:40',
                'entry_start' => '08:00',
                'late_until' => '08:10',
                'exit_time' => '14:00',
                'active' => true,
            ]);
        }

        $this->command->info('Usuarios de prueba (password para todos: "password"):');
        $this->command->info('Admin:      admin@colegio.test');
        $this->command->info('Profesor:   profesor@colegio.test');
        $this->command->info('Padre:      padre@colegio.test');
        $this->command->info('Estudiante: estudiante@colegio.test');
        $this->command->info('QR del estudiante Juan López: ' . $student->qr_token);
        $this->command->info('Código de barras: ' . $student->barcode);
    }
}
