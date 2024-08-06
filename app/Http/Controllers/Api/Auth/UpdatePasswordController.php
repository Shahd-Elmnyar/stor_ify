<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdatePasswordRequest;

class UpdatePasswordController extends Controller
{
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'exists:users'],
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
            ], [
                'email.required' => 'EMAIL_REQUIRED',
                'email.email' => 'EMAIL_INVALID_FORMAT',
                'email.exists' => 'USER_NOT_FOUND',
                'password.required' => 'PASSWORD_REQUIRED',
                'password.min' => 'PASSWORD_INVALID_FORMAT',
                'password.regex' => 'PASSWORD_INVALID_FORMAT',
                'password.confirmed' => 'PASSWORD_NOT_MATCHED',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse(
                    $validator->errors()->first()
                );
            }
            $user = User::where('email', $request->email)->first();

            if (!$user || !$user->otp_validated) {
                return response()->json([
                    'code' => 'INVALID_EMAIL_OR_OTP'
                ], 401);
            }

            $user->update(['password' => Hash::make($request->password), 'otp_validated' => false]);

            return $this->successResponse();
        } catch (\Exception $e) {
            Log::error('Error during password update process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }
}
