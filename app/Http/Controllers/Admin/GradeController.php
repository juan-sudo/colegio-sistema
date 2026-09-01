<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCriterion;
use App\Models\Course;
use App\Models\CriterionGrade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function index(Request $request): Response
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();

        $selectedCourseId = $request->get('course_id');
        $selectedCourse = null;
        $students = collect();
        $courseCriteria = collect();
        $grades = collect();

        if ($selectedCourseId) {
            $selectedCourse = Course::with(['gradeSection', 'teacher', 'students'])->findOrFail($selectedCourseId);
            $students = $selectedCourse->students()->orderBy('last_name')->get();

            $courseCriteria = AssessmentCriterion::where('course_id', $selectedCourseId)
                ->with('evaluationCriteria')
                ->get();

            $grades = CriterionGrade::whereIn('assessment_criterion_id', $courseCriteria->pluck('id'))
                ->get()
                ->keyBy(fn ($item) => $item->assessment_criterion_id . '-' . $item->student_id);
        }

        return Inertia::render('Admin/Grades/Index', [
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'students' => $students,
            'courseCriteria' => $courseCriteria,
            'grades' => $grades,
        ]);
    }
}
