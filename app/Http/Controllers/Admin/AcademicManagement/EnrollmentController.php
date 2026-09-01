<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Enrollment\StoreEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\GradeSection;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Enrollment::with(['student', 'gradeSection', 'academicYear'])
            ->when($request->academic_year_id, fn ($q, $id) => $q->where('academic_year_id', $id))
            ->when($request->grade_section_id, fn ($q, $id) => $q->where('grade_section_id', $id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s));

        $enrollments = $this->applySort($query, $request, ['status', 'enrollment_date'], 'enrollment_date', 'desc')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return Inertia::render('Admin/Enrollments/Index', [
            'enrollments' => $enrollments,
            'students' => Student::with('user')->get(),
            'gradeSections' => GradeSection::all(['id', 'name', 'level']),
            'academicYears' => AcademicYear::all(['id', 'name']),
            'filters' => [
                'per_page' => $this->perPage($request),
                'sort_by' => $request->sort_by,
                'sort_dir' => $request->sort_dir,
            ],
        ]);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        Enrollment::create($request->validated());

        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula registrada correctamente.');
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'grade_section_id' => 'required|exists:grade_sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $enrollment->update($data);

        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula actualizada correctamente.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return back()->with('success', 'Matrícula eliminada correctamente.');
    }
}
