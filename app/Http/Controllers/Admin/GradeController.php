<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCriterion;
use App\Models\Course;
use App\Models\CriterionGrade;
use App\Models\EvaluationCriteria;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with(['gradeSection', 'teacher', 'assessmentCriteria.evaluationCriteria', 'assessmentCriteria.course'])->get();

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

        return view('admin.grades.index', compact(
            'courses',
            'selectedCourse',
            'students',
            'courseCriteria',
            'grades'
        ));
    }
}