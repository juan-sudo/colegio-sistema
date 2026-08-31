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

class EvaluationCriteriaController extends Controller
{
    public function index(Request $request)
    {
        $criteria = EvaluationCriteria::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with(['assessmentCriteria' => fn ($q) => $q->with('course')])
            ->paginate(20);

        foreach ($criteria as $criterion) {
            foreach ($criterion->assessmentCriteria as $ac) {
                $ac->students = $ac->course->students()->orderBy('last_name')->get();
                $ac->grades = CriterionGrade::where('assessment_criterion_id', $ac->id)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('admin.academic.evaluation-criteria.index', compact('criteria'));
    }

    public function create()
    {
        return view('admin.academic.evaluation-criteria.create');
    }

    public function store(StoreEvaluationCriteriaRequest $request)
    {
        EvaluationCriteria::create($request->validated());

        return redirect()->route('admin.academic.evaluation-criteria.index')->with('success', 'Criterio de evaluación creado correctamente.');
    }

    public function edit(EvaluationCriteria $evaluationCriterion)
    {
        return view('admin.academic.evaluation-criteria.edit', compact('evaluationCriterion'));
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

    public function grades(Request $request, EvaluationCriteria $evaluationCriterion)
    {
        $courses = Course::with(['gradeSection', 'teacher'])->get();
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

            return view('admin.academic.evaluation-criteria.grades', compact(
                'evaluationCriterion', 'course', 'students', 'grades', 'assessmentCriterion'
            ));
        }

        return view('admin.academic.evaluation-criteria.select-course', compact(
            'evaluationCriterion', 'courses'
        ));
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
