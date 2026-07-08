<?php

namespace App\Listeners;

use App\Events\TransactionOrdered;
use App\Mail\TransactionOrderedMail;
use App\Services\FileGenerateService;
use App\Services\TelegramNotificationService;
use Illuminate\Support\Facades\Mail;

class SendTransactionOrderedNotification
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
    public function handle(TransactionOrdered $event): void
    {
        // Generate Invoice PDF
        $fileGenerateService = app(FileGenerateService::class);
        $result = $fileGenerateService->generateInvoice($event->transaction);

        // Kirim email dengan attachment invoice
        Mail::to($event->transaction->customer_email)
            ->send(new TransactionOrderedMail($event->transaction));

        // Kirim telegram notif
        $chatId = config('services.telegram.chat_id');
        app(TelegramNotificationService::class)
            ->sendMessage($chatId, "Transaction Ordered: {$event->transaction->code}");
    }
}
