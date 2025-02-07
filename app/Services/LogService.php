<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService
{
    public function info($channel, $message)
    {
        // create logic to log message
        Log::channel($channel)->info($message);
    }
}
