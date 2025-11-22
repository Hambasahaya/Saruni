<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class PasswordVerifyTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string', 'regex:/^\d{6}$/'],
        ];
    }
}
