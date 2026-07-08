<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserResource;
use App\Services\ErrorHandler;
use App\Services\FileUploadService;
use App\Services\UserService;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class UserController extends Controller
{
    use ValidatesRequests;

    protected $errorHandler;

    protected $fileUploadService;

    protected $userService;

    public function __construct(
        ErrorHandler $errorHandler,
        FileUploadService $fileUploadService,
        UserService $userService
    ) {
        $this->errorHandler = $errorHandler;
        $this->fileUploadService = $fileUploadService;
        $this->userService = $userService;
    }

    public function profile(
        Request $request
    ) {
        try {
            $userLogin = Auth::user();

            return response()->json([
                'status' => 'success',
                'data' => new UserResource($userLogin),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function saveProfile(
        Request $request
    ) {
        try {
            $user = auth('api')->user();
            $user = $this->userService->updateProfile($request, $user);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $user,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function uploadProfilePicture(
        Request $request
    ) {
        try {
            $result = $this->userService->updateProfilePicture($request);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile picture updated successfully',
                'data' => new UserResource($result),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}
