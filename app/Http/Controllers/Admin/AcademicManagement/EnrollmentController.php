<?php

namespace App\Http\Controllers\Admin\AcademicManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Enrollment\StoreEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\GradeSection;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = Enrollment::with(['student', 'gradeSection', 'academicYear'])
            ->when($request->academic_year_id, fn ($q, $id) => $q->where('academic_year_id', $id))
            ->when($request->grade_section_id, fn ($q, $id) => $q->where('grade_section_id', $id))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('enrollment_date')
            ->paginate(20);

        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        return view('admin.enrollments.create', [
            'students' => Student::with('user')->get(),
            'gradeSections' => GradeSection::all(),
            'academicYears' => AcademicYear::all(),
        ]);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        Enrollment::create($request->validated());

        return redirect()->route('admin.enrollments.index')->with('success', 'Matrícula registrada correctamente.');
    }

    public function edit(Enrollment $enrollment)
    {
        return view('admin.enrollments.edit', [
            'enrollment' => $enrollment,
            'students' => Student::with('user')->get(),
            'gradeSections' => GradeSection::all(),
            'academicYears' => AcademicYear::all(),
        ]);
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
