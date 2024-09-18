<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        $userLogin = auth()->user();
        $user = User::find($userLogin->Oid);

        // Generate refresh token (random string)
        $refreshToken = Str::random(60);

        // Store JWT access token, refresh token, and expiration time in the user table
        $user->update([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_expires_at' => Carbon::now()->addMinutes(30),  // Set token expiration for 30 minutes
        ]);

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'expires_in' => JWTAuth::factory()->getTTL() * 60  // 30 minutes (in seconds)
        ]);
    }

    // Get the authenticated user
    public function getUser(Request $request)
    {
        return response()->json(auth()->user());
    }

    // Refresh access token using refresh token
    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        // Find user by refresh token
        $user = User::where('refresh_token', $refreshToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }

        // Check if the refresh token is expired or revoked
        if ($user->isAccessTokenExpired()) {
            return response()->json(['error' => 'Access token expired, please log in again'], 401);
        }

        // Generate a new JWT access token
        $newToken = JWTAuth::fromUser($user);

        // Update the user with the new access token and token expiration time
        $user->update([
            'access_token' => $newToken,
            'token_expires_at' => Carbon::now()->addMinutes(30),  // New expiration for 30 minutes
        ]);

        return response()->json([
            'access_token' => $newToken,
            'expires_in' => JWTAuth::factory()->getTTL() * 60  // In seconds
        ]);
    }

    public function logout()
    {
        $userLogin = auth()->user();
        $user = User::find($userLogin->Oid);

        // Invalidate the JWT token
        JWTAuth::invalidate(JWTAuth::getToken());

        // Clear the stored tokens in the database
        $user->update([
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null,
        ]);

        return response()->json(['message' => 'Logged out successfully']);
    }
}
