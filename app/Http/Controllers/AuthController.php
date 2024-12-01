<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
// use App\Services\GoogleServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // protected $googleService;

    // public function __construct(GoogleService $googleService)
    // {
    //     $this->googleService = $googleService;
    // }


    // Register a new user
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Return a success response with the newly created user
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    }

    // Login and generate token
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the credentials are valid
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        // If successful, generate a token
        $user = User::find(auth()->user()->id);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');
        $userInfo = $this->googleService->getUserInfo($code);

        return response()->json($userInfo);
    }
}
