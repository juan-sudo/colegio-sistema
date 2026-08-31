<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\GradeSection;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with(["gradeSection", "teacher"])
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("school_year", "like", "%{$search}%"))
            ->paginate(20);

        $gradeSections = GradeSection::all();
        $teachers = Teacher::with("user")->get();

        return view("admin.courses.index", compact("courses", "gradeSections", "teachers"));
    }

    public function create()
    {
        return view("admin.courses.create", [
            "gradeSections" => GradeSection::all(),
            "teachers" => Teacher::with("user")->get(),
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        $data = $request->validated();

        $course = Course::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Curso registrado correctamente.', 'course' => $course]);
        }

        return redirect()->route("admin.courses.index")->with("success", "Curso registrado correctamente.");
    }

    public function edit(Course $course)
    {
        return view("admin.courses.edit", [
            "course" => $course,
            "gradeSections" => GradeSection::all(),
            "teachers" => Teacher::with("user")->get(),
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $data = $request->validated();

        $course->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Curso actualizado correctamente.', 'course' => $course]);
        }

        return redirect()->route("admin.courses.index")->with("success", "Curso actualizado correctamente.");
    }

    public function destroy(Course $course)
    {
        $course->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Curso eliminado correctamente.']);
        }

        return back()->with("success", "Curso eliminado correctamente.");
    }
}
