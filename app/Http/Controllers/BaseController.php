<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\ErrorHandler;

class BaseController extends Controller
{
    protected $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function languages(Request $request)
    {
        try {
            $languages = [
                [
                    'name' => 'English',
                    'code' => 'en'
                ],
                [
                    'name' => 'Indonesia',
                    'code' => 'id'
                ],
            ];

            return response()->json([
                'status' => 'success',
                'data' => $languages
            ]);
        } catch (Exception $e) {
            $this->errorHandler->handleError($e);
        }
    }
}
