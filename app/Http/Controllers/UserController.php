<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ErrorHandler;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use ValidatesRequests;

    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function profile(Request $request)
    {
        try {
            $userLogin = Auth::user();

            $userProfile = [
                'id' => $userLogin->id,
                'name' => $userLogin->name,
                'email' => $userLogin->email,
                'created_at' => Carbon::parse($userLogin->created_at)->format('l jS F Y'),
                "profile_picture_url" => $userLogin->profile_picture_url,
                "username" => $userLogin->username,
                "phone" => $userLogin->phone,
                "address" => $userLogin->address,
                "birth_date" => $userLogin->birth_date,
                "gender" => $userLogin->gender,
                "email_verified_at" => $userLogin->email_verified_at,
                "country" => $userLogin->country ? $userLogin->country->only('id', 'name') : null,
                "city" => $userLogin->city ? $userLogin->city->only('id', 'name') : null,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $userProfile
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function saveProfile(User $user, Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'username' => 'nullable|string|max:25',
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'birth_date' => 'nullable|date_format:Y-m-d',
                'gender' => 'nullable|string|max:10',
            ]);

            DB::transaction(function () use ($user, $validated) {
                $user->update($validated);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $user->only(
                    'id',
                    'name',
                    'email',
                    'phone',
                    'profile_picture_url',
                    'username',
                    'address',
                    'birth_date',
                    'gender',
                    'created_at',
                    'updated_at'
                )
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}
