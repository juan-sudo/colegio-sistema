<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route("teacher")?->id ?? $this->route("teacher");

        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "email" => "required|email|unique:users,email,{$teacherId}",
            "code" => "required|string|unique:teachers,code,{$teacherId}",
            "specialty" => "nullable|string|max:100",
            "phone" => "nullable|string",
            "active" => "boolean",
        ];
    }
}
