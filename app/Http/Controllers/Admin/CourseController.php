<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\GradeSection;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Course::with(["gradeSection", "subject", "teacher"])
            ->when($request->search, fn ($q, $search) => $q->where("name", "like", "%{$search}%")
                ->orWhere("school_year", "like", "%{$search}%"));

        $courses = $this->applySort($query, $request, ["name", "school_year"], "name")
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render("Admin/Courses/Index", [
            "courses" => $courses,
            "gradeSections" => GradeSection::all(["id", "name", "level"]),
            "subjects" => Subject::all(["id", "name"]),
            "teachers" => Teacher::with("user")->get(),
            "filters" => [
                "search" => $request->search,
                "per_page" => $this->perPage($request),
                "sort_by" => $request->sort_by,
                "sort_dir" => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        Course::create($request->validated());

        return redirect()->route("admin.courses.index")->with("success", "Curso registrado correctamente.");
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return redirect()->route("admin.courses.index")->with("success", "Curso actualizado correctamente.");
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with("success", "Curso eliminado correctamente.");
    }
}
