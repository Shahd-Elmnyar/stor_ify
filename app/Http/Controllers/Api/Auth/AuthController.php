<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
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
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
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
                'password.min' => 'PASSWORD_INVALID_FORMAT',
                'password.regex' => 'PASSWORD_INVALID_FORMAT',
                'password.confirmed' => 'PASSWORD_NOT_MATCHED',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 'ERROR',
                'data' => $validator->errors()], 422);
        }

        // Check if the username or email already exists
        if (User::where('username', $request->username)->exists()) {
            return response()->json([
                'code' => 'ERROR',
                'data'=>'USERNAME_TAKEN',
                ], 409);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'code' =>'ERROR',
                'data'=> 'EMAIL_TAKEN',
                ], 409);
        }

        // Retrieve the user role
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json([
                'code' =>'ERROR',
                'data'=> 'USER_ROLE_NOT_FOUND',
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
            return ApiResponse::success($userData,token:$token);
            // return response()->json([
            //     'code' => 'SUCCESS',
            //     'token' => $token,
            //     'data' => $userData,
            // ]);
        } catch (QueryException $e) {
            return response()->json(['msg' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['msg' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    public function logout(Request $request)
{
    // Check if user is authenticated
    if (!$request->user()) {
        return response()->json([
            'code'=>'ERROR',
            'data' => 'USER_NOT_AUTH'], 401);
    }

    // Attempt to delete the token
    try {
        // Check if the current access token is valid
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        } else {
            return response()->json([
                'code' =>'ERROR',
                'data'=>'INVALID_TOKEN',
                ], 401);
        }

        return response()->json([
            "code" => 'SUCCESS',
            'data'=>[]
            ], 200);
    } catch (\Exception $e) {
        return response()->json(['code' => 'ERROR: ' . $e->getMessage()], 500);
    }
}



    // Login a user
    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:5|max:30'
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'code' => 'ERROR',
                'data' => $validator->errors()], 422);
        }
        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'code' => 'ERROR',
                'data' => 'INVALID_CREDENTIALS'], 401);
        }

        try {
            // Generate and return token on successful login
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);
            return ApiResponse::success($userData,token:$token);
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
