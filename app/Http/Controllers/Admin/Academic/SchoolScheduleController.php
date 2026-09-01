<?php

namespace App\Http\Controllers\Admin\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\UpdateSchoolScheduleRequest;
use App\Models\SchoolSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolScheduleController extends Controller
{
    public function index(Request $request): Response
    {
        $schedules = SchoolSchedule::orderBy('id')->get()->map->toHumanArray();

        return Inertia::render('Admin/SchoolSchedule/Index', [
            'schedules' => $schedules,
        ]);
    }

    public function update(UpdateSchoolScheduleRequest $request, SchoolSchedule $schoolSchedule)
    {
        $schoolSchedule->update($request->validated());

        return back()->with('success', 'Horario del colegio actualizado correctamente.');
    }
}
