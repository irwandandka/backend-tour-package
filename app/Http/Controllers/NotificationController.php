<?php

namespace App\Http\Controllers;

use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramNotificationService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function notifyUser(Request $request)
    {
        $chatId = ''; // Replace with your chat ID
        $message = 'This is a test notification!';

        if ($this->telegramService->sendMessage($chatId, $message)) {
            return response()->json(['status' => 'Message sent successfully!']);
        }

        return response()->json(['status' => 'Failed to send message!'], 500);
    }
}
