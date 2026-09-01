<?php

namespace App\Http\Requests\Admin\Academic;

use App\Models\SchoolSchedule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolSchedule = SchoolSchedule::current();
        $entryStart = $schoolSchedule?->entry_start?->format('H:i');
        $exitTime = $schoolSchedule?->exit_time?->format('H:i');

        return [
            'course_id' => ['required', 'exists:courses,id'],
            'shift_id' => ['nullable', 'exists:shifts,id'],
            'day_of_week' => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado'],
            'start_time' => array_filter([
                'required',
                'date_format:H:i',
                $entryStart ? "after_or_equal:{$entryStart}" : null,
            ]),
            'end_time' => array_filter([
                'required',
                'date_format:H:i',
                'after:start_time',
                $exitTime ? "before_or_equal:{$exitTime}" : null,
            ]),
            'classroom' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        $schoolSchedule = SchoolSchedule::current();

        return [
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'day_of_week.in' => 'Selecciona un día válido de la semana.',
            'start_time.after_or_equal' => "El horario debe iniciar dentro del horario oficial del colegio (desde las {$schoolSchedule?->entry_start?->format('H:i')}).",
            'end_time.before_or_equal' => "El horario debe terminar dentro del horario oficial del colegio (hasta las {$schoolSchedule?->exit_time?->format('H:i')}).",
        ];
    }
}
