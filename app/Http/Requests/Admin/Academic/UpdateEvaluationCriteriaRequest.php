<?php

namespace App\Http\Requests\Admin\Academic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEvaluationCriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ];
    }
}
