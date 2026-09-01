<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required|string|max:100",
            "subject_id" => "required|exists:subjects,id",
            "grade_section_id" => "required|exists:grade_sections,id",
            "teacher_id" => "nullable|exists:teachers,id",
            "school_year" => "required|string|max:20",
        ];
    }
}
