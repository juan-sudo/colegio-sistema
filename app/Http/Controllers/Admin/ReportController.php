<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AccountingExport;
use App\Exports\AttendanceReportExport;
use App\Exports\GradesReportExport;
use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradeSection;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();
        $gradeSections = GradeSection::all();

        return view('admin.reports.index', compact('courses', 'gradeSections'));
    }

    public function attendance(Request $request)
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();
        $selectedCourseId = $request->get('course_id');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        $attendances = collect();
        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'total' => 0,
        ];

        if ($selectedCourseId) {
            $attendances = Attendance::with(['student', 'course'])
                ->where('course_id', $selectedCourseId)
                ->whereDate('date', '>=', $dateFrom)
                ->whereDate('date', '<=', $dateTo)
                ->orderByDesc('date')
                ->get();

            $stats = [
                'present' => $attendances->where('status', 'presente')->count(),
                'late' => $attendances->where('status', 'tardanza')->count(),
                'absent' => $attendances->where('status', 'falta')->count(),
                'total' => $attendances->count(),
            ];
        }

        return view('admin.reports.attendance', compact('courses', 'selectedCourseId', 'dateFrom', 'dateTo', 'attendances', 'stats'));
    }

    public function grades(Request $request)
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();
        $selectedCourseId = $request->get('course_id');
        $grades = collect();
        $stats = [
            'average' => 0,
            'approved' => 0,
            'failed' => 0,
            'total' => 0,
        ];

        if ($selectedCourseId) {
            $grades = Grade::with(['student', 'course', 'gradePeriod'])
                ->where('course_id', $selectedCourseId)
                ->orderByDesc('created_at')
                ->get();

            $scores = $grades->pluck('score')->filter();
            $stats = [
                'average' => $scores->count() > 0 ? round($scores->avg(), 2) : 0,
                'approved' => $grades->where('score', '>=', 11)->count(),
                'failed' => $grades->where('score', '<', 11)->count(),
                'total' => $grades->count(),
            ];
        }

        return view('admin.reports.grades', compact('courses', 'selectedCourseId', 'grades', 'stats'));
    }

    public function students(Request $request)
    {
        $gradeSections = GradeSection::all();
        $selectedGradeSectionId = $request->get('grade_section_id');
        $students = collect();
        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
        ];

        if ($selectedGradeSectionId) {
            $students = Student::with(['user', 'gradeSection', 'courses'])
                ->where('grade_section_id', $selectedGradeSectionId)
                ->orderBy('last_name')
                ->get();

            $stats = [
                'total' => $students->count(),
                'active' => $students->where('active', true)->count(),
                'inactive' => $students->where('active', false)->count(),
            ];
        }

        return view('admin.reports.students', compact('gradeSections', 'selectedGradeSectionId', 'students', 'stats'));
    }

    public function payments(Request $request)
    {
        $type = $request->get('type');
        $status = $request->get('status');
        $payments = Payment::with('student')
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('due_date')
            ->get();

        $stats = [
            'total_amount' => $payments->sum('amount'),
            'total_paid' => $payments->sum('paid'),
            'total_balance' => $payments->sum('balance'),
            'total_count' => $payments->count(),
        ];

        return view('admin.reports.payments', compact('payments', 'stats', 'type', 'status'));
    }

    public function exportPayments(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');

        return Excel::download(new PaymentsExport($type, $status), 'pagos.xlsx');
    }

    public function exportAccounting(Request $request)
    {
        $type = $request->query('type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return Excel::download(new AccountingExport($type, $dateFrom, $dateTo), 'contabilidad.xlsx');
    }

    public function exportGrades(Request $request)
    {
        $courseId = $request->query('course_id');
        $periodId = $request->query('period_id');

        return Excel::download(new GradesReportExport($courseId, $periodId), 'notas.xlsx');
    }

    public function exportAttendance(Request $request)
    {
        $courseId = $request->query('course_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        return Excel::download(new AttendanceReportExport($courseId, $dateFrom, $dateTo), 'asistencia.xlsx');
    }
}
