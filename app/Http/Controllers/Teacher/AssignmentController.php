<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\GradeSubmissionRequest;
use App\Http\Requests\Teacher\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->withCount("submissions")->latest()->get();
        return view("teacher.assignments.index", compact("course", "assignments"));
    }

    public function store(StoreAssignmentRequest $request, Course $course)
    {
        $data = $request->validated();

        if ($request->hasFile("file")) {
            $data["file_path"] = $request->file("file")->store("assignments", "public");
        }

        $course->assignments()->create([
            ...$data,
            "created_by" => auth()->id(),
        ]);

        return back()->with("success", "Tarea publicada correctamente.");
    }

    public function submissions(Assignment $assignment)
    {
        $submissions = $assignment->submissions()->with("student")->get();
        return view("teacher.assignments.submissions", compact("assignment", "submissions"));
    }

    public function grade(GradeSubmissionRequest $request, \App\Models\Submission $submission)
    {
        $data = $request->validated();

        $submission->update([...$data, "status" => "calificado"]);

        return back()->with("success", "Entrega calificada.");
    }
}
