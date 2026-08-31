<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuardianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guardianId = $this->route("guardian")?->id ?? $this->route("guardian");

        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "email" => "required|email|unique:users,email,{$guardianId}",
            "phone_whatsapp" => "required|string",
            "phone" => "nullable|string",
        ];
    }
}
