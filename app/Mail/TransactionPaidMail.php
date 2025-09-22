<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your E-Ticket #{$this->transaction->code}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transaction.paid',
            with: [
                'transaction' => $this->transaction,
            ]
        );
    }

    public function attachments(): array
    {
        $url = $this->transaction->eticket->url; // URL dari DB (S3/MinIO)

        // ambil konten file dari URL
        $pdfContent = file_get_contents($url);

        return [
            Attachment::fromData(
                fn() => $pdfContent,
                "E-Ticket-{$this->transaction->code}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
