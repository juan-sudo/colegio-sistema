<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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
            "code" => "required|string|unique:teachers,code",
            "specialty" => "nullable|string|max:100",
            "phone" => "nullable|string",
        ];
    }
}
