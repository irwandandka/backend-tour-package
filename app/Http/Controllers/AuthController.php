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
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    private $errorHandler;
    protected $googleService;

    public function __construct(GoogleService $googleService, ErrorHandler $errorHandler)
    {
        $this->googleService = $googleService;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @see SwaggerInfo::register()
     */
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

    /**
     * @see SwaggerInfo::login()
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            // Check if the credentials are valid
            $user = User::where('email', $credentials['email'])->first();
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json(['message' => 'Invalid login credentials'], 401);
            }

            // If successful, generate a token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Set token expiration to 3 days
            $token->accessToken->expires_at = Carbon::now()->addDays(3);
            $token->accessToken->save();

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::profile()
     */
    public function profile(Request $request)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $request->user(),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::redirectToGoogle()
     */
    public function redirectToGoogle(Request $request)
    {
        try {
            $redirectUri = $request->query('redirect_uri');

            if (!$redirectUri) {
                return response()->json(['message' => 'redirect_uri is required'], 400);
            }

            $authUrl = $this->googleService->getAuthUrl($redirectUri);

            return response()->json(['url' => $authUrl]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::handleGoogleCallback()
     */
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
                    'avatar' => $userData['avatar'],
                    'password' => bcrypt(Str::random(32)), // if user doesn't exist, set a random password, let them reset it later
                ]
            );

            $tokenResult = $user->createToken('authToken');

            // Update expired_at di personal_access_tokens
            $tokenModel = $user->tokens()->latest()->first();
            $tokenModel->expires_at = now()->addDays(3);
            $tokenModel->save();

            // Redirect ke app menggunakan custom scheme + data
            $redirectUrl = "tour-package://redirect?token=" . $tokenResult->plainTextToken . "&email=" . urlencode($user->email);
            return redirect()->away($redirectUrl);
        } catch (\Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @see SwaggerInfo::logout()
     */
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
