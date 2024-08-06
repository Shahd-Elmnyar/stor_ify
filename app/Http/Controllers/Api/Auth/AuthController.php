<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required','string','min:4','max:255','unique:users'],
            'email' => ['required','email','max:255','unique:users'],
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
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse(
                $validator->errors()->first());
        }
        // Retrieve the user role
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'code' => 'USER_ROLE_NOT_FOUND',
                ], 500);
        }

        // Attempt to create the user and catch any database-related errors
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
                'token'=> $token]);
            // return response()->json([
            //     'token' => $token,
            //     'user' => $userData,
            // ], 200);
        } catch (QueryException $e) {
            return response()->json(['msg' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function logout(Request $request)
    {
        // Check if user is authenticated

        try {
            // Check if the current access token is valid
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



    // Login a user
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => ['required','email','max:255','exists:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',              // must be at least 8 characters long
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/\d/',         // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain at least one special character
            ],
            ], [
                'email.required' => 'EMAIL_REQUIRED',
                'email.email' => 'EMAIL_INVALID_FORMAT',
                'email.exists' => 'USER_NOT_FOUND',
                'password.required' =>  'PASSWORD_REQUIRED',
                'password.min' => 'PASSWORD_INVALID_FORMAT',
                'password.regex' => 'PASSWORD_INVALID_FORMAT',

        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->first());
        }
        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' =>'INVALID_CREDENTIALS'], 401);
        }

        try {
            // Generate and return token on successful login
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return $this->successResponse([
                'user' => $userData,
                'token' => $token
            ]);
            // return response()->json([
            //     'code' => 'SUCCESS',
            //     'token' => $token,
            //     'data' => $userData
            // ]);
        } catch (\Exception $e) {
            // Handle any unexpected exceptions during token creation
            return response()->json(['msg' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

}
