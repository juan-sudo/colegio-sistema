<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreScheduleRequest;
use App\Http\Requests\Admin\Academic\UpdateScheduleRequest;
use App\Models\GradeSection;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with(['gradeSection', 'subject', 'teacher', 'shift'])
            ->when($request->grade_section_id, fn ($q, $id) => $q->where('grade_section_id', $id))
            ->when($request->teacher_id, fn ($q, $id) => $q->where('teacher_id', $id))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(50);

        return view('admin.academic.schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.academic.schedules.create', [
            'gradeSections' => GradeSection::all(),
            'subjects' => Subject::all(),
            'teachers' => Teacher::with('user')->get(),
            'shifts' => Shift::all(),
        ]);
    }

    public function store(StoreScheduleRequest $request)
    {
        Schedule::create($request->validated());

        return redirect()->route('admin.academic.schedules.index')->with('success', 'Horario creado correctamente.');
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.academic.schedules.edit', [
            'schedule' => $schedule,
            'gradeSections' => GradeSection::all(),
            'subjects' => Subject::all(),
            'teachers' => Teacher::with('user')->get(),
            'shifts' => Shift::all(),
        ]);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return redirect()->route('admin.academic.schedules.index')->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Horario eliminado correctamente.');
    }
}
