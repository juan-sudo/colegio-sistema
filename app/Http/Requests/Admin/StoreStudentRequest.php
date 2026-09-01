<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:6",
            "dni" => "required|string|unique:students,dni",
            "grade_section_id" => "required|exists:grade_sections,id",
            "birth_date" => "nullable|date",
            "phone" => "nullable|string",
            "guardian_id" => "nullable|integer|exists:guardians,id",
        ];
    }
}
