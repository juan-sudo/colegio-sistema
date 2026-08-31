<?php

namespace App\Http\Requests\Admin\Academic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id ?? $this->route('subject');

        return [
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:subjects,code,'.$subjectId,
            'description' => 'nullable|string',
        ];
    }
}
