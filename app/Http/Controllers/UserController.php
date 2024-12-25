<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\ErrorHandler;
use Exception;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
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
            ];

            return response()->json([
                'status' => 'success',
                'data' => $userProfile
            ]);
        } catch (Exception $e) {
            return $this->errorHandler->handleError($e);
        }
    }
}
