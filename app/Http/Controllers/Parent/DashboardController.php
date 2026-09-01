<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $guardian = auth()->user()->guardian;
        $students = $guardian->students()->with("gradeSection")->get();

        return Inertia::render("Parent/Dashboard", compact("students"));
    }

    public function grades(\App\Models\Student $student): Response
    {
        $this->authorizeGuardian($student);

        $grades = $student->grades()->with(["course", "gradePeriod"])->get()->groupBy("course.name");
        return Inertia::render("Parent/Grades", compact("student", "grades"));
    }

    public function attendance(\App\Models\Student $student): Response
    {
        $this->authorizeGuardian($student);

        $attendances = $student->attendances()->orderByDesc("date")->paginate(30);
        return Inertia::render("Parent/Attendance", compact("student", "attendances"));
    }

    private function authorizeGuardian(\App\Models\Student $student): void
    {
        $guardian = auth()->user()->guardian;
        if (!$guardian->students()->where("students.id", $student->id)->exists()) {
            abort(403);
        }
    }
}
