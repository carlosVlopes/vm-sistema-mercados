<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'password' => 'required|min:6',
            'password_confirm' => 'required|min:6|same:password',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password_confirm.required' => 'A confirmação de senha é obrigatória.',
            'password_confirm.min' => 'A confirmação de senha deve ter no mínimo 6 caracteres.',
            'password_confirm.same' => 'A confirmação de senha deve ser igual à senha.',
        ];
    }
}
