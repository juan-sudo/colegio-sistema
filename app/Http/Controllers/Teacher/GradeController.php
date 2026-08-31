<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ImportGradesRequest;
use App\Http\Requests\Teacher\StoreGradeRequest;
use App\Imports\GradesImport;
use App\Models\Course;
use App\Models\Grade;
use App\Models\GradePeriod;
use App\Models\AssessmentCriterion;
use App\Models\CriterionGrade;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GradeController extends Controller
{
    public function index(Course $course)
    {
        $periods = GradePeriod::orderBy("start_date")->get();
        $students = $course->students()->orderBy("last_name")->get();
        $grades = Grade::where("course_id", $course->id)->get()->groupBy("student_id");

        $criteria = AssessmentCriterion::where('course_id', $course->id)->orderBy('name')->get();
        return view("teacher.grades.index", compact("course", "periods", "students", "grades", "criteria"));
    }

    public function store(StoreGradeRequest $request, Course $course)
    {
        $data = $request->validated();

        foreach ($data["scores"] as $studentId => $score) {
            if ($score === null || $score === "") continue;

            Grade::updateOrCreate(
                [
                    "student_id" => $studentId,
                    "course_id" => $course->id,
                    "grade_period_id" => $data["grade_period_id"],
                    "evaluation" => $data["evaluation"] ?? "General",
                ],
                ["score" => $score, "recorded_by" => auth()->id()]
            );
        }

        return back()->with("success", "Notas guardadas correctamente.");
    }

    public function importForm(Course $course)
    {
        return view("teacher.grades.import", compact("course"));
    }

    public function import(ImportGradesRequest $request, Course $course)
    {
        Excel::import(
            new GradesImport($course->id, $request->grade_period_id, auth()->id()),
            $request->file("file")
        );

        return back()->with("success", "Notas importadas correctamente desde Excel.");
    }

    public function downloadTemplate(Course $course)
    {
        $students = $course->students()->orderBy("last_name")->get();

        return Excel::download(
            new \App\Exports\GradesTemplateExport($students),
            "plantilla_notas_{$course->name}.xlsx"
        );
    }

    public function storeCriteria(Request $request, Course $course)
    {
        $data = $request->validate(['assessment_criterion_id'=>'required|exists:assessment_criteria,id','scores'=>'required|array','scores.*'=>'nullable|numeric|min:0|max:100']);
        $criterion = AssessmentCriterion::where('course_id', $course->id)->findOrFail($data['assessment_criterion_id']);
        foreach ($data['scores'] as $studentId => $score) {
            if ($score === null || $score === '') continue;
            CriterionGrade::updateOrCreate(['assessment_criterion_id'=>$criterion->id,'student_id'=>$studentId], ['score'=>$score,'recorded_by'=>auth()->id()]);
        }
        return back()->with('success', 'Notas por criterio guardadas correctamente.');
    }
}
