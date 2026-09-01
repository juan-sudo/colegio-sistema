<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreScheduleRequest;
use App\Http\Requests\Admin\Academic\UpdateScheduleRequest;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\SchoolSchedule;
use App\Models\Shift;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $classroom = $request->string('classroom')->toString();
        $teacherId = $request->input('teacher_id');
        $weekStart = $this->resolveWeekStart($request->get('week'));

        $weekDates = collect(range(0, 4))->map(
            fn ($i) => $weekStart->copy()->addDays($i)->toDateString()
        );

        $schedulesQuery = Schedule::with([
            'course.subject',
            'course.gradeSection',
            'course.teacher',
        ]);

        if ($classroom !== '') {
            $schedulesQuery->where('classroom', $classroom);
        }

        if ($teacherId) {
            $schedulesQuery->whereHas('course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            });
        }

        $schedules = $schedulesQuery
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        $schedulesByDay = array_fill_keys($days, []);
        foreach ($schedules as $sch) {
            if (isset($schedulesByDay[$sch->day_of_week])) {
                $schedulesByDay[$sch->day_of_week][] = $sch;
            }
        }

        $classrooms = Schedule::query()
            ->whereNotNull('classroom')
            ->where('classroom', '<>', '')
            ->select('classroom')
            ->distinct()
            ->orderBy('classroom')
            ->pluck('classroom')
            ->values()
            ->all();

        $courses = Course::with(['subject', 'gradeSection', 'teacher'])
            ->orderBy('name')
            ->get();

        $shifts = Shift::orderBy('name')->get();

        $teachers = Teacher::with('user')
            ->orderBy('first_name')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => trim(($t->first_name ?? '') . ' ' . ($t->last_name ?? '')),
                    'code' => $t->code,
                ];
            })
            ->values()
            ->all();

        $schoolSchedule = SchoolSchedule::current();
        $academicYear = AcademicYear::query()->where('is_current', true)->first();
        $schoolYearStart = $academicYear?->start_date?->toDateString() ?? $weekDates->first() ?? now()->startOfYear()->toDateString();
        $schoolYearEnd = $academicYear?->end_date?->toDateString() ?? $weekDates->last() ?? now()->endOfYear()->toDateString();

        return inertia('Admin/Academic/Schedules/Index', [
            'classroom' => $classroom ?: null,
            'classrooms' => $classrooms,
            'weeks' => $weekDates->all(),
            'schedulesByDay' => $schedulesByDay,
            'courses' => $courses,
            'shifts' => $shifts,
            'teachers' => $teachers,
            'teacherId' => $teacherId ? (int) $teacherId : null,
            'schoolHours' => $schoolSchedule ? [
                'name' => $schoolSchedule->name,
                'start' => $schoolSchedule->entry_start?->format('H:i'),
                'end' => $schoolSchedule->exit_time?->format('H:i'),
            ] : null,
            'schoolYear' => [
                'start' => $schoolYearStart,
                'end' => $schoolYearEnd,
            ],
            'holidayDates' => $this->buildHolidayDates($schoolYearStart, $schoolYearEnd),
            'filters' => [
                'classroom' => $classroom,
                'teacher_id' => $teacherId,
                'week' => $weekStart->toDateString(),
            ],
        ]);
    }

    public function store(StoreScheduleRequest $request)
    {
        $data = $request->validated();

        if (empty($data['shift_id'])) {
            $data['shift_id'] = null;
        }

        $schedule = Schedule::create($data);

        return back()->with('success', 'Bloque del horario registrado correctamente.');
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $data = $request->validated();

        if (empty($data['shift_id'])) {
            $data['shift_id'] = null;
        }

        $schedule->update($data);

        return back()->with('success', 'Bloque del horario actualizado correctamente.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Bloque del horario eliminado correctamente.');
    }

    private function resolveWeekStart(mixed $value): Carbon
    {
        try {
            $date = $value ? Carbon::parse($value) : now();
        } catch (\Throwable $e) {
            $date = now();
        }

        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);

        if ($startOfWeek->isFuture() && $startOfWeek->diffInDays(now()) > 7) {
            $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        }

        return $startOfWeek;
    }

    private function buildHolidayDates(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $dates = [];

        $yearStart = $start->year;
        $yearEnd = $end->year;

        foreach (range($yearStart, $yearEnd) as $year) {
            $dates[] = sprintf('%s-%s-%s', $year, '01', '01');
            $dates[] = sprintf('%s-%s-%s', $year, '05', '01');
            $dates[] = sprintf('%s-%s-%s', $year, '07', '28');
            $dates[] = sprintf('%s-%s-%s', $year, '10', '08');
            $dates[] = sprintf('%s-%s-%s', $year, '12', '25');

            $easter = Carbon::createFromTimestamp(strtotime("{$year}-03-21 + " . (easter_days($year) ?: 0) . " days"));
            $dates[] = $easter->copy()->subDays(2)->toDateString();
            $dates[] = $easter->copy()->addDays(1)->toDateString();
        }

        return collect($dates)
            ->filter(fn ($date) => Carbon::parse($date)->between($start, $end, equal: true))
            ->unique()
            ->values()
            ->all();
    }
}
