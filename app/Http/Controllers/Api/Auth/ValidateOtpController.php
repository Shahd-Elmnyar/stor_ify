<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateOtpRequest;
use Illuminate\Support\Facades\Validator;

class ValidateOtpController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function validateOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'exists:users'],
                'otp' => ['required', 'max:4'],
            ], [
                'email.required' => 'EMAIL_REQUIRED',
                'email.email' => 'INVALID_EMAIL',
                'email.exists' => 'USER_NOT_FOUND',
                'otp.required' => 'OTP_REQUIRED',
                'otp.max' => 'INVALID_OTP_FORMAT',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse(
                    $validator->errors()->first()
                );
            }

            $otp2 = $this->otp->validate($request->email, $request->otp);
            if (!$otp2->status) {
                $msg = $otp2->message == "OTP is not valid" || $otp2->message == "OTP does not exist" ? 'INVALID_OTP' : $otp2->message;
                return response()->json([
                    'code' => $msg
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $user->update(['otp_validated' => true]);

            return $this->successResponse();
        } catch (\Exception $e) {
            Log::error('Error during OTP validation process: ' . $e->getMessage());

            return $this->genericErrorResponse();
        }
    }
}
// $otp2 = $this->otp->validate($request->email, $request->otp);

// if (!$otp2->status) {
//     return response()->json(['code' => 'ERROR', 'data' => $otp2], 401);
// }

// $user = User::where('email', $request->email)->first();
// $user->update(['otp_validated' => true]);

// return response()->json(['code' => 'SUCCESS', 'data' => []], 200);
