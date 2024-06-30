<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' =>['required' ,'email','exists:users'],
            'otp' =>['required' ,'max:4'],
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'EMAIL_REQUIRED',
            'otp.required' => 'OTP_REQUIRED',
            'email.email' => 'INVALID_EMAIL',
            'email.exists' => 'USER_NOT_FOUND',
        ];
    }
}
