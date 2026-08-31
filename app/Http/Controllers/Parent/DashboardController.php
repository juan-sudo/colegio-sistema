<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $guardian = auth()->user()->guardian;
        $students = $guardian->students()->with("gradeSection")->get();

        return view("parent.dashboard", compact("students"));
    }

    public function grades(\App\Models\Student $student)
    {
        $this->authorizeGuardian($student);

        $grades = $student->grades()->with(["course", "gradePeriod"])->get()->groupBy("course.name");
        return view("parent.grades", compact("student", "grades"));
    }

    public function attendance(\App\Models\Student $student)
    {
        $this->authorizeGuardian($student);

        $attendances = $student->attendances()->orderByDesc("date")->paginate(30);
        return view("parent.attendance", compact("student", "attendances"));
    }

    private function authorizeGuardian(\App\Models\Student $student): void
    {
        $guardian = auth()->user()->guardian;
        if (!$guardian->students()->where("students.id", $student->id)->exists()) {
            abort(403);
        }
    }
}
