<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreSubmissionRequest;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function index(): Response
    {
        $student = auth()->user()->student;
        $courses = $student->courses()->with(["gradeSection", "assignments" => function ($q) {
            $q->latest();
        }])->get();

        return Inertia::render("Student/Dashboard", compact("courses"));
    }

    public function store(StoreSubmissionRequest $request, Assignment $assignment)
    {
        $student = auth()->user()->student;
        $path = $request->file("file")->store("submissions", "public");

        $status = $assignment->due_date && now()->gt($assignment->due_date) ? "tarde" : "entregado";

        $assignment->submissions()->updateOrCreate(
            ["student_id" => $student->id],
            ["file_path" => $path, "submitted_at" => now(), "status" => $status]
        );

        return back()->with("success", "Trabajo entregado correctamente.");
    }
}
