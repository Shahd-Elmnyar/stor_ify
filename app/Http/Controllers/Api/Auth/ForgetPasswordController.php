<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
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
                'email.exists' => 'USER_NOT_FOUND',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'code' => 'ERROR',
                    'data' => 'USER_NOT_FOUND',
                ], 404);
            }

            $user->notify(new ResetPasswordVerificationNotification());

            return response()->json([
                'code' => 'SUCCESS',
                'data' => [],
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during forget password process: ' . $e->getMessage());

            return response()->json([
                'code' => 'ERROR',
                'data' => 'GENERIC_ERROR',
            ], 500);
        }
    }
}

// try {
//     $input = $request->only('email');
//     $user = User::where('email', $input['email'])->first();

//     if (!$user) {
//         return response()->json([
//             'code' => 'ERROR',
//             'msg' => 'USER_NOT_FOUND',
//         ], 404);
//     }

//     $user->notify(new ResetPasswordVerificationNotification());

//     return response()->json([
//         'code' => 'SUCCESS',
//         'data' => [],
//     ], 200);

// } catch (\Exception $e) {
//     Log::error('Error during forget password process: ' . $e->getMessage());

//     return response()->json([
//         'code' => 'ERROR',
//         'msg' => 'GENERIC_ERROR',
//     ], 500);
// }
