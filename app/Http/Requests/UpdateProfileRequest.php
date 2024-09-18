<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id,
            'img' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'INVALID_EMAIL',
            'email.unique' => 'USER_NOT_FOUND',
            'img.image' => 'MUST_BE_IMAGE',
            'img.mimes'=>'IMAGE_FORMAT_NOT_CORRECT'
        ];
    }
}

