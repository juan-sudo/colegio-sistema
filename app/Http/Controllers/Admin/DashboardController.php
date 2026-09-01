<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\GradeSection;
use App\Models\Guardian;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
    {
        public function index(): Response
        {
            $stats = [
                'students' => Student::where('active', true)->count(),
                'teachers' => Teacher::count(),
                'guardians' => Guardian::count(),
                'courses' => Course::count(),
                'enrollments' => Enrollment::where('status', 'matriculado')->count(),
                'payments_pending' => Payment::where('status', 'pendiente')->count(),
                'payments_overdue' => Payment::where('status', 'vencido')->count(),
                'attendance_today' => Attendance::whereDate('date', now()->toDateString())->count(),
                'income_month' => (float) Payment::whereBetween('paid_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('paid'),
                ];

        return Inertia::render('Admin/Dashboard', [
                               'stats' => $stats,
                               'attendanceTrend' => $this->attendanceTrend(),
                               'paymentsTrend' => $this->paymentsTrend(),
                               'sectionsBreakdown' => $this->sectionsBreakdown(),
                               'paymentsStatus' => $this->paymentsStatusBreakdown(),
                               ]);
        }

private function attendanceTrend(): array
        {
            $start = now()->subDays(13)->startOfDay();

        $rows = DB::table('attendances')
            ->select('date', 'status', DB::raw('COUNT(*) as total'))
            ->where('date', '>=', $start->toDateString())
            ->groupBy('date', 'status')
            ->get();

        $byDate = [];
            foreach ($rows as $row) {
                $byDate[$row->date][$row->status] = (int) $row->total;
            }

        $days = [];
            for ($i = 0; $i < 14; $i++) {
                $date = $start->copy()->addDays($i);
                $key = $date->toDateString();
                $counts = $byDate[$key] ?? [];
                $days[] = [
                    'date' => $key,
                    'label' => $date->translatedFormat('d M'),
                    'presente' => $counts['presente'] ?? 0,
                    'tardanza' => $counts['tardanza'] ?? 0,
                    'falta' => ($counts['falta'] ?? 0) + ($counts['justificado'] ?? 0),
                    ];
            }

        return $days;
        }

private function paymentsTrend(): array
        {
            $start = now()->subMonths(5)->startOfMonth();

        $collected = DB::table('payments')
            ->select('paid_date', 'paid')
            ->whereNotNull('paid_date')
            ->where('paid_date', '>=', $start->toDateString())
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->paid_date)->format('Y-m'))
            ->map(fn ($rows) => $rows->sum('paid'));

        $pending = DB::table('payments')
            ->select('due_date', 'amount', 'discount', 'paid')
            ->where('status', '!=', 'pagado')
            ->where('due_date', '>=', $start->toDateString())
            ->get()
            ->groupBy(fn ($row) => Carbon::parse($row->due_date)->format('Y-m'))
            ->map(fn ($rows) => $rows->sum(fn ($row) => $row->amount - $row->discount - $row->paid));

        $months = [];
            for ($i = 0; $i < 6; $i++) {
                $month = $start->copy()->addMonths($i);
                $key = $month->format('Y-m');
                $months[] = [
                    'month' => $key,
                    'label' => ucfirst($month->translatedFormat('M')),
                    'cobrado' => (float) ($collected[$key] ?? 0),
                    'pendiente' => max(0, (float) ($pending[$key] ?? 0)),
                    ];
            }

        return $months;
        }

private function sectionsBreakdown(): array
        {
            return GradeSection::query()
                ->withCount(['students' => fn ($q) => $q->where('active', true)])
                ->orderByDesc('students_count')
                ->take(6)
                ->get()
                ->map(fn (GradeSection $section) => [
                    'label' => $section->name,
                    'value' => $section->students_count,
                    ])
                ->values()
                ->all();
        }

private function paymentsStatusBreakdown(): array
        {
            $rows = DB::table('payments')
                ->select('status', DB::raw('COUNT(*) as total'), DB::raw('SUM(amount - discount) as amount'))
                ->groupBy('status')
                ->get()
                ->keyBy('status');

        $labels = [
            'pagado' => 'Pagado',
            'pendiente' => 'Pendiente',
            'vencido' => 'Vencido',
            ];

        return collect($labels)->map(function ($label, $status) use ($rows) {
            $row = $rows->get($status);

                                     return [
                                         'status' => $status,
                                         'label' => $label,
                                         'count' => (int) ($row->total ?? 0),
                                         'amount' => (float) ($row->amount ?? 0),
                                         ];
        })->values()->all();
        }
    }
