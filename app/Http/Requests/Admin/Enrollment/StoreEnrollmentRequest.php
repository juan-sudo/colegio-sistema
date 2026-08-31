<?php

namespace App\Http\Requests\Admin\Enrollment;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'grade_section_id' => 'required|exists:grade_sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
