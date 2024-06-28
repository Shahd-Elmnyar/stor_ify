<?php

namespace App\Http\Controllers\Api;

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
                'password.min' => 'The password must be at least 8 characters long.',
                'password.regex' => 'The password format is invalid. It must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
                'password.confirmed' => 'The password confirmation does not match.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the username or email already exists
        if (User::where('username', $request->username)->exists()) {
            return response()->json(['msg' => 'Username already exists'], 409);
        }

        if (User::where('email', $request->email)->exists()) {
            return response()->json(['msg' => 'Email already exists'], 409);
        }

        // Retrieve the user role
        $userRole = Role::where('name', 'user')->first();
        if (!$userRole) {
            return response()->json(['msg' => 'User role not found'], 500);
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

            return response()->json([
                'code' => 'SUCCESS',
                'token' => $token,
                'user' => $userData,
            ]);
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
        return response()->json(['msg' => 'User not authenticated'], 401);
    }

    // Attempt to delete the token
    try {
        // Check if the current access token is valid
        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        } else {
            return response()->json(['msg' => 'Invalid token'], 401);
        }

        return response()->json(["code" => 'SUCCESS'], 200);
    } catch (\Exception $e) {
        return response()->json(['msg' => 'Logout failed: ' . $e->getMessage()], 500);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['msg' => 'Invalid credentials'], 401);
        }

        try {
            // Generate and return token on successful login
            $token = $user->createToken('auth-token')->plainTextToken;
            $userData = new UserResource($user);

            return response()->json([
                'code' => 'SUCCESS',
                'token' => $token,
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            // Handle any unexpected exceptions during token creation
            return response()->json(['msg' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

}
