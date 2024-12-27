<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;
use App\Services\ErrorHandler;
use Throwable;

class AuthController extends Controller
{
    private $errorHandler;
    protected $googleService;

    public function __construct(GoogleService $googleService, ErrorHandler $errorHandler)
    {
        $this->googleService = $googleService;
        $this->errorHandler = $errorHandler;
    }

    // Register a new user
    public function register(Request $request)
    {
        try {
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
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    // Login and generate token
    public function login(Request $request)
    {
        try {
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
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function redirectToGoogle()
    {
        try {
            $authUrl = $this->googleService->getAuthUrl();

            return response()->json(['url' => $authUrl]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $userData = $this->googleService->getUserData($request->input('code'));

            // Cari atau buat pengguna di database
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'google_id' => $userData['id'],
                    'avatar' => $userData['avatar']
                ]
            );

            // Generate token untuk klien
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}
