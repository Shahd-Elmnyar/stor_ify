<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' =>['required' ,'email','unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',              // must be at least 8 characters long
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/\d/',         // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain at least one special character
                'confirmed'
            ],
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'INVALID_EMAIL',
            'email.unique' => 'USER_NOT_FOUND',
            'password.required' => 'PASSWORD_REQUIRED',
            'password.min,regex' => 'PASSWORD_INVALID_FORMAT',
            'Password.confirmed' => 'PASSWORD_NOT_MATCHED',
        ];
    }
}
