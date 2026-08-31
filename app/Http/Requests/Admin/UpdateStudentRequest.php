<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route("student")?->id ?? $this->route("student");

        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "email" => "required|email|unique:users,email,{$studentId}",
            "dni" => "required|string|unique:students,dni,{$studentId}",
            "grade_section_id" => "required|exists:grade_sections,id",
            "birth_date" => "nullable|date",
            "phone" => "nullable|string",
            "active" => "boolean",
        ];
    }
}
