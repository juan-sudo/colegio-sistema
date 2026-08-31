<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => "required|string|max:150",
            "description" => "nullable|string",
            "due_date" => "nullable|date",
            "file" => "nullable|file|max:20480",
        ];
    }
}
