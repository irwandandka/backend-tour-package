<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class TelegramNotificationService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token'); // Ensure you set this in your .env file
    }

    public function sendMessage($chatId, $message)
    {
        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        return $response->successful();
    }

    public function sendErrorToProgrammer($request, $error, $user)
    {
        // Check instance from
        if (! $request instanceof Request) {
            return;
        }
        if (! $error instanceof Throwable) {
            return;
        }

        // Get the error detail
        $errorMessage = $error->getMessage();
        $errorLine = $error->getLine();
        $errorFile = $error->getFile();
        $fullUrl = $request->fullUrl();
        $requestFrom = $request->header('User-Agent');
        $clientIP = $request->ip();

        // Format the error message
        $formattedMessage = "<strong style='color: red;'>An error occurred:</strong>\n";
        $formattedMessage .= "<strong>Message:</strong> {$errorMessage}\n";
        $formattedMessage .= "<strong>Line:</strong> {$errorLine}\n";
        $formattedMessage .= "<strong>File:</strong> {$errorFile}\n";
        $formattedMessage .= "<strong>Full URL:</strong> {$fullUrl}\n";
        $formattedMessage .= "<strong>Request From:</strong> {$requestFrom}\n";
        $formattedMessage .= "<strong>Client IP:</strong> {$clientIP}\n";
        if ($user) {
            $formattedMessage .= "<strong>User Login:</strong> {$user->UserName}";
        }

        $chatId = env('TELEGRAM_BOT_CHAT_ID');
        $token = env('TELEGRAM_BOT_TOKEN');

        // Prepare the payload
        $data = [
            'chat_id' => $chatId,
            'text' => $formattedMessage,
            'parse_mode' => 'HTML',  // Optional: Allows HTML formatting in the message
        ];

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        try {
            $response = Http::post($url, $data);
        } catch (Throwable $error) {
            return;
        }
    }
}
