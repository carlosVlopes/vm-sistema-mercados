<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ClientPasswordRegister extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
            'password_confirm' => ['required', 'same:password'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter pelo menos um número.',
            'password_confirm.required' => 'A confirmação de senha é obrigatória.',
            'password_confirm.same' => 'A confirmação de senha deve ser igual à senha.',
        ];
    }
}
