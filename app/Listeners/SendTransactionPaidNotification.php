<?php

namespace App\Listeners;

use App\Events\TransactionPaid;
use App\Mail\TransactionPaidMail;
use App\Services\FileGenerateService;
use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Mail;

class SendTransactionPaidNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionPaid $event): void
    {
        // Generate Eticket PDF
        $fileGenerateService = app(FileGenerateService::class);
        $result = $fileGenerateService->generateEticket($event->transaction);

        // Kirim email dengan attachment eticket
        Mail::to($event->transaction->customer_email)
            ->send(new TransactionPaidMail($event->transaction));

        // Kirim telegram notif
        $chatId = config('services.telegram.chat_id');
        app(TelegramNotificationService::class)
            ->sendMessage($chatId, "Transaction Paid: {$event->transaction->code}");
    }
}
