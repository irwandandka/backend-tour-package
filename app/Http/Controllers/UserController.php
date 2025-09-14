<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ErrorHandler;
use App\Services\FileUploadService;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Services\R2Service;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    use ValidatesRequests;

    protected $errorHandler;
    protected $r2Service;
    protected $fileUploadService;
    protected $userService;

    public function __construct(
        ErrorHandler $errorHandler,
        R2Service $r2Service,
        FileUploadService $fileUploadService,
        UserService $userService
    ) {
        $this->errorHandler = $errorHandler;
        $this->r2Service = $r2Service;
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
                'data' => new UserResource($userLogin)
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function saveProfile(
        User $user,
        Request $request
    ) {
        try {
            $user = $this->userService->updateProfile($request, $user);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
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
