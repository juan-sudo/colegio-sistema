<?php

namespace App\Http\Requests\Admin\Academic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'entry_window_start' => ['required', 'date_format:H:i'],
            'entry_start' => ['required', 'date_format:H:i'],
            'late_until' => ['required', 'date_format:H:i', 'after:entry_start'],
            'exit_time' => ['required', 'date_format:H:i', 'after:late_until'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'late_until.after' => 'El límite de tardanza debe ser posterior a la hora de entrada.',
            'exit_time.after' => 'La hora de salida debe ser posterior al límite de tardanza.',
        ];
    }
}
