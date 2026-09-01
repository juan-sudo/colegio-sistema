<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academic\StoreEvaluationCriteriaRequest;
use App\Http\Requests\Admin\Academic\UpdateEvaluationCriteriaRequest;
use App\Models\AssessmentCriterion;
use App\Models\Course;
use App\Models\CriterionGrade;
use App\Models\EvaluationCriteria;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EvaluationCriteriaController extends Controller
{
    public function index(Request $request): Response
    {
        $criteria = EvaluationCriteria::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with(['assessmentCriteria' => fn ($q) => $q->with('course.gradeSection')])
            ->paginate($this->perPage($request))
            ->withQueryString();

        foreach ($criteria as $criterion) {
            foreach ($criterion->assessmentCriteria as $ac) {
                $ac->setAttribute('students', $ac->course->students()->orderBy('last_name')->get());
                $ac->setAttribute('grades', CriterionGrade::where('assessment_criterion_id', $ac->id)
                    ->get()
                    ->keyBy('student_id'));
            }
        }

        return Inertia::render('Admin/Academic/EvaluationCriteria/Index', [
            'criteria' => $criteria,
            'filters' => ['search' => $request->search, 'per_page' => $this->perPage($request)],
        ]);
    }

    public function store(StoreEvaluationCriteriaRequest $request)
    {
        EvaluationCriteria::create($request->validated());

        return redirect()->route('admin.academic.evaluation-criteria.index')->with('success', 'Criterio de evaluación creado correctamente.');
    }

    public function update(UpdateEvaluationCriteriaRequest $request, EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->update($request->validated());

        return redirect()->route('admin.academic.evaluation-criteria.index')->with('success', 'Criterio de evaluación actualizado correctamente.');
    }

    public function destroy(EvaluationCriteria $evaluationCriterion)
    {
        $evaluationCriterion->delete();

        return back()->with('success', 'Criterio de evaluación eliminado correctamente.');
    }

    public function grades(Request $request, EvaluationCriteria $evaluationCriterion): Response
    {
        $courseId = $request->get('course_id');

        if ($courseId) {
            $course = Course::with(['gradeSection', 'teacher'])->findOrFail($courseId);
            $students = $course->students()->orderBy('last_name')->get();

            $assessmentCriterion = AssessmentCriterion::where('course_id', $courseId)
                ->where('evaluation_criteria_id', $evaluationCriterion->id)
                ->first();

            if (!$assessmentCriterion) {
                $assessmentCriterion = AssessmentCriterion::create([
                    'course_id' => $courseId,
                    'name' => $evaluationCriterion->name,
                    'description' => $evaluationCriterion->description,
                    'maximum_score' => 100,
                ]);
            }

            $grades = CriterionGrade::where('assessment_criterion_id', $assessmentCriterion->id)
                ->get()
                ->keyBy('student_id');

            return Inertia::render('Admin/Academic/EvaluationCriteria/Grades', [
                'evaluationCriterion' => $evaluationCriterion,
                'course' => $course,
                'students' => $students,
                'grades' => $grades,
                'assessmentCriterion' => $assessmentCriterion,
                'courses' => [],
            ]);
        }

        return Inertia::render('Admin/Academic/EvaluationCriteria/Grades', [
            'evaluationCriterion' => $evaluationCriterion,
            'course' => null,
            'students' => [],
            'grades' => [],
            'assessmentCriterion' => null,
            'courses' => Course::with(['gradeSection', 'teacher'])->get(),
        ]);
    }

    public function storeGrades(Request $request, EvaluationCriteria $evaluationCriterion)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $courseId = $request->course_id;

        $assessmentCriterion = AssessmentCriterion::where('course_id', $courseId)
            ->where('evaluation_criteria_id', $evaluationCriterion->id)
            ->first();

        if (!$assessmentCriterion) {
            $course = Course::findOrFail($courseId);
            $assessmentCriterion = AssessmentCriterion::create([
                'course_id' => $courseId,
                'name' => $evaluationCriterion->name,
                'description' => $evaluationCriterion->description,
                'maximum_score' => 100,
                'evaluation_criteria_id' => $evaluationCriterion->id,
            ]);
        }

        foreach ($request->scores as $studentId => $score) {
            if ($score === null || $score === '') {
                continue;
            }

            CriterionGrade::updateOrCreate(
                [
                    'assessment_criterion_id' => $assessmentCriterion->id,
                    'student_id' => $studentId,
                ],
                [
                    'score' => $score,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Notas registradas correctamente.');
    }

    public function storeGradesByAssessment(Request $request, AssessmentCriterion $assessmentCriterion)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $studentId => $score) {
            if ($score === null || $score === '') {
                continue;
            }

            CriterionGrade::updateOrCreate(
                [
                    'assessment_criterion_id' => $assessmentCriterion->id,
                    'student_id' => $studentId,
                ],
                [
                    'score' => $score,
                    'recorded_by' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Notas registradas correctamente.');
    }
}
