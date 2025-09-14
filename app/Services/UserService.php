<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function updateProfile(
        Request $request,
        User $user
    ) {
        $validated = $request->validate([
            'username' => 'nullable|string|max:25',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|string|max:10',
        ]);

        return DB::transaction(function () use ($user, $validated) {
            $user->update($validated);
        });
    }

    public function updateProfilePicture(
        Request $request,
    ) {
        return DB::transaction(function () use ($request) {
            $user = Auth::user();

            $userData = User::find($user->id);

            $request->validate([
                'image' => 'required|image|max:10240', // max 10MB
            ]);

            $fileUploadService = app(FileUploadService::class);

            if ($userData->profile_picture_url) {
                $parsed = parse_url($userData->profile_picture_url);

                if (isset($parsed['path'])) {
                    // Buang leading slash, misal "/profile_pictures/abc.webp" -> "profile_pictures/abc.webp"
                    $oldPath = ltrim($parsed['path'], '/' . env('MINIO_BUCKET'));
                    $fileUploadService->deleteFile($oldPath);
                }
            }

            $generateWebP = true;
            $fileDir = 'profile_pictures';
            $result = $fileUploadService->uploadImage($request->file('image'), $fileDir, $generateWebP);

            if (isset($result['webp'])) {
                $imageURL = $result['webp'];

                $userData->profile_picture_url = $imageURL;
                $userData->save();
            }

            return $userData;
        });
    }
}
