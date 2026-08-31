<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "grade_period_id" => "required|exists:grade_periods,id",
            "evaluation" => "nullable|string|max:100",
            "scores" => "required|array",
            "scores.*" => "nullable|numeric|min:0|max:20",
        ];
    }
}
