<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            ],
        ], [
            'username.required' => 'USERNAME_REQUIRED',
            'username.min' => 'USERNAME_INVALID_FORMAT',
            'username.unique' => 'USERNAME_TAKEN',
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'EMAIL_INVALID_FORMAT',
            'email.unique' => 'EMAIL_TAKEN',
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

        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'code' => 'USER_ROLE_NOT_FOUND',
            ], 500);
        }

        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse([
                'user' => $userData,
                'token' => $token
            ]);
        } catch (QueryException $e) {
            return response()->json(['msg' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            if ($request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            } else {
                return $this->unauthorizedResponse();
            }
            return $this->successResponse();
        } catch (\Exception $e) {
            return $this->genericErrorResponse('auth.error_occurred', ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/\d/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'email.required' => 'EMAIL_REQUIRED',
            'email.email' => 'EMAIL_INVALID_FORMAT',
            'email.exists' => 'USER_NOT_FOUND',
            'password.required' => 'PASSWORD_REQUIRED',
            'password.min' => 'PASSWORD_INVALID_FORMAT',
            'password.regex' => 'PASSWORD_INVALID_FORMAT',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => 'INVALID_CREDENTIALS'
            ], 401);
        }

        try {
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse([
                'user' => $userData,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }
}
