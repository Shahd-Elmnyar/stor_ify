<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:255',
            'phone' => 'required|string|max:15|min:10',
            'address' => 'required|string|max:255',
            'date' => 'nullable|after:today|date_format:Y-m-d',
            'time' => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'USERNAME_REQUIRED',
            'username.max' => 'USERNAME_MAX',
            'phone.required' => 'PHONE_REQUIRED',
            'phone.max' => 'PHONE_MAX',
            'phone.min' => 'PHONE_MIN',
            'address.required' => 'ADDRESS_REQUIRED',
            'address.max' => 'ADDRESS_MAX',
            'date.after' => 'DATE_AFTER_TODAY',
            'date.date_format' => 'DATE_FORMAT',
            'time.date_format' => 'TIME_FORMAT',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $firstError = '';

        foreach ($errors as $messages) {
            $firstError = $messages[0]; // Get the first error message
            break; // Exit the loop after getting the first error message
        }

        throw new HttpResponseException(response()->json([
            'code'=>$firstError], 422));
    }
}
