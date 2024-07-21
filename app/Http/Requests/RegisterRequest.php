<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => ['required', 'string', 'min:4', 'max:255', 'unique:users'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/\d/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ]
        ];
    }
    public function messages()
    {
        return[
            'username.required' => 'USERNAME_REQUIRED',
            'username.min' => 'USERNAME_INVALID_FORMAT',
            'username.unique' => 'USERNAME_TAKEN',
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'EMAIL_INVALID_FORMAT',
            'email.unique' => 'EMAIL_TAKEN',
            'password.required' =>  'PASSWORD_REQUIRED',
            'password.min' => 'PASSWORD_INVALID_FORMAT',
            'password.regex' => 'PASSWORD_INVALID_FORMAT',
            'password.confirmed' => 'PASSWORD_NOT_MATCHED',
        ];
    }
    
}
