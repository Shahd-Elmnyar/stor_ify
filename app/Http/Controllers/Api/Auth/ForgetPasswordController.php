<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ForgetPasswordRequest;
use App\Notifications\ResetPasswordVerificationNotification;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ], [
                'email.required' => 'EMAIL_REQUIRED',
                'email.email' => 'EMAIL_INVALID_FORMAT',
                'email.exists' => 'USER_NOT_FOUND',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => $validator->errors()->first(),
                ], 422);
            }
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->notFoundResponse('USER_NOT_FOUND');
            }

            $user->notify(new ResetPasswordVerificationNotification());
            return response()->json([
                'code' => 'SUCCESS',
                'data' => (object)[],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }
}
