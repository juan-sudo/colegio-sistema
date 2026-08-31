<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingEntry;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'guardians' => Guardian::count(),
            'courses' => Course::count(),
            'enrollments' => Enrollment::where('status', 'matriculado')->count(),
            'payments_pending' => Payment::where('status', 'pendiente')->count(),
            'payments_overdue' => Payment::where('status', 'vencido')->count(),
            'attendance_today' => Attendance::whereDate('date', now()->toDateString())->count(),
            'grades_recorded' => Grade::whereDate('created_at', now()->toDateString())->count(),
        ];

        $financial = [
            'income' => AccountingEntry::income()->sum('amount'),
            'expense' => AccountingEntry::expense()->sum('amount'),
            'fixed_cost' => AccountingEntry::fixedCost()->sum('amount'),
        ];

        $recentPayments = Payment::with('student')->latest('paid_date')->take(5)->get();
        $recentAttendances = Attendance::with(['student', 'course'])->latest('date')->take(5)->get();
        $recentGrades = Grade::with(['student', 'course', 'gradePeriod'])->latest('created_at')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'financial', 'recentPayments', 'recentAttendances', 'recentGrades'));
    }
}

