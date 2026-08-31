<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class ImportGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "file" => "required|mimes:xlsx,xls,csv",
            "grade_period_id" => "required|exists:grade_periods,id",
        ];
    }
}
