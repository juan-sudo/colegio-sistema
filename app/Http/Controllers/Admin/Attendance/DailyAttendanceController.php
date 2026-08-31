<?php

namespace App\Http\Controllers\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\WhatsAppService;

class DailyAttendanceController extends Controller
{
    public function __construct(private WhatsAppService $whatsapp) {}

    public function index(Request $request)
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();
        $selectedCourseId = $request->get('course_id');
        $date = $request->get('date', now()->toDateString());
        $course = null;
        $students = collect();
        $attendances = collect();
        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'total' => 0,
        ];

        if ($selectedCourseId) {
            $course = Course::with(['gradeSection', 'teacher', 'students'])->findOrFail($selectedCourseId);
            $students = $course->students()->orderBy('last_name')->get();
            $attendances = Attendance::where('course_id', $selectedCourseId)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');

            $stats = [
                'present' => $attendances->where('status', 'presente')->count(),
                'late' => $attendances->where('status', 'tardanza')->count(),
                'absent' => $attendances->where('status', 'falta')->count(),
                'total' => $students->count(),
            ];
        }

        return view('admin.attendance.index', compact('courses', 'selectedCourseId', 'date', 'course', 'students', 'attendances', 'stats'));
    }

    public function general(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $students = Student::with(['gradeSection', 'courses'])
            ->orderBy('last_name')
            ->get();

        $attendances = Attendance::whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $stats = [
            'present' => $attendances->where('status', 'presente')->count(),
            'late' => $attendances->where('status', 'tardanza')->count(),
            'absent' => $attendances->where('status', 'falta')->count(),
            'total' => $students->count(),
        ];

        return view('admin.attendance.general', compact('students', 'attendances', 'stats', 'date'));
    }

    public function report(Request $request)
    {
        $courseId = $request->get('course_id');
        $date = $request->get('date', now()->toDateString());

        $course = Course::with(['gradeSection', 'teacher'])->findOrFail($courseId);
        $students = $course->students()->orderBy('last_name')->get();

        $attendances = Attendance::where('course_id', $courseId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        $stats = [
            'present' => $attendances->where('status', 'presente')->count(),
            'late' => $attendances->where('status', 'tardanza')->count(),
            'absent' => $attendances->where('status', 'falta')->count(),
            'total' => $students->count(),
        ];

        return view('admin.attendance.report', compact('course', 'students', 'attendances', 'stats', 'date'));
    }

    public function scanner(Request $request)
    {
        $courseId = $request->get('course_id');
        $course = Course::with(['gradeSection', 'teacher'])->findOrFail($courseId);
        $courses = Course::with(['gradeSection', 'teacher'])->get();

        return view('admin.attendance.scanner', compact('course', 'courses'));
    }

    public function registrar(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'method' => 'required|in:qr,barcode,biometric,manual',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $student = Student::query()
            ->where('qr_token', $request->code)
            ->orWhere('barcode', $request->code)
            ->orWhere('biometric_id', $request->code)
            ->orWhere('code', $request->code)
            ->firstOrFail();

        $ahora = Carbon::now();
        $horaPresente = Carbon::parse('07:00:00');
        $horaTarde = Carbon::parse('07:10:00');
        
        if ($ahora->lt($horaPresente)) {
            $status = 'presente';
        } elseif ($ahora->lte($horaTarde)) {
            $status = 'tardanza';
        } else {
            $status = 'falta';
        }

        $courseId = $request->course_id;
        if (!$courseId) {
            $courseId = $student->courses()->first()?->id;
        }

        if (!$courseId) {
            return response()->json([
                'ok' => false,
                'message' => 'El alumno no está matriculado en ningún curso.',
            ], 422);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $courseId,
                'date' => $ahora->toDateString(),
            ],
            [
                'time_in' => $ahora->toTimeString(),
                'status' => $status,
                'method' => $request->method,
                'recorded_by' => auth()->id(),
            ]
        );

        return response()->json([
            'ok' => true,
            'student' => $student->fullName(),
            'course' => $student->courses->first()->name ?? 'Sin curso',
            'grade_section' => $student->gradeSection->name ?? '-',
            'status' => $status,
            'time' => $ahora->format('H:i:s'),
        ]);
    }

    public function registrarGeneral(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'method' => 'required|in:qr,barcode,biometric,manual',
        ]);

        $student = Student::query()
            ->where('qr_token', $request->code)
            ->orWhere('barcode', $request->code)
            ->orWhere('biometric_id', $request->code)
            ->orWhere('code', $request->code)
            ->firstOrFail();

        $ahora = Carbon::now();
        $horaPresente = Carbon::parse('07:00:00');
        $horaTarde = Carbon::parse('07:10:00');
        
        if ($ahora->lt($horaPresente)) {
            $status = 'presente';
        } elseif ($ahora->lte($horaTarde)) {
            $status = 'tardanza';
        } else {
            $status = 'falta';
        }

        $courseId = $student->courses()->first()?->id;

        if (!$courseId) {
            return response()->json([
                'ok' => false,
                'message' => 'El alumno no está matriculado en ningún curso.',
            ], 422);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'course_id' => $courseId,
                'date' => $ahora->toDateString(),
            ],
            [
                'time_in' => $ahora->toTimeString(),
                'status' => $status,
                'method' => $request->method,
                'recorded_by' => auth()->id(),
            ]
        );

        return response()->json([
            'ok' => true,
            'student' => $student->fullName(),
            'course' => $student->courses->first()->name ?? 'Sin curso',
            'grade_section' => $student->gradeSection->name ?? '-',
            'status' => $status,
            'time' => $ahora->format('H:i:s'),
        ]);
    }

    public function manual(Request $request)
    {
        $courseId = $request->get('course_id');
        $date = $request->get('date', now()->toDateString());

        $course = Course::with(['gradeSection', 'teacher'])->findOrFail($courseId);
        $students = $course->students()->orderBy('last_name')->get();

        $attendances = Attendance::where('course_id', $courseId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('admin.attendance.manual', compact('course', 'students', 'attendances', 'date'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:presente,tardanza,falta,justificado',
            'attendances.*.time_in' => 'nullable|date_format:H:i',
            'attendances.*.course_id' => 'nullable|exists:courses,id',
            'attendances.*.observation' => 'nullable|string|max:500',
        ]);

        $date = $request->date;

        foreach ($request->attendances as $studentId => $data) {
            $courseId = $data['course_id'] ?? null;
            if (!$courseId) {
                $course = \App\Models\Student::findOrFail($studentId)->courses()->first();
                $courseId = $course?->id;
            }

            if (!$courseId) {
                continue;
            }

            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'date' => $date,
                ],
                [
                    'status' => $data['status'],
                    'time_in' => $data['time_in'] ?? null,
                    'method' => 'manual',
                    'recorded_by' => auth()->id(),
                    'observation' => $data['observation'] ?? null,
                ]
            );

            if ($data['status'] === 'falta') {
                $student = \App\Models\Student::findOrFail($studentId);
                $this->whatsapp->notificarFalta($student, $attendance);
            }
        }

        return back()->with('success', 'Asistencia registrada correctamente.');
    }

    public function markAbsences(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'date' => 'required|date',
        ]);

        $course = Course::findOrFail($request->course_id);
        $date = $request->date;

        $yaRegistrados = Attendance::where('course_id', $course->id)
            ->where('date', $date)
            ->pluck('student_id');

        $faltantes = $course->students()->whereNotIn('students.id', $yaRegistrados)->get();

        foreach ($faltantes as $student) {
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'date' => $date,
                'status' => 'falta',
                'method' => 'manual',
                'recorded_by' => auth()->id(),
            ]);

            $this->whatsapp->notificarFalta($student, $attendance);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Se marcaron {$faltantes->count()} inasistencias y se enviaron las alertas por WhatsApp."]);
        }

        return back()->with('success', "Se marcaron {$faltantes->count()} inasistencias y se enviaron las alertas por WhatsApp.");
    }
}
