<?php

namespace App\Services;

use App\Models\{ETicket, Invoice, Status, Transaction};
use Illuminate\Support\Facades\View;
use Barryvdh\Snappy\Facades\SnappyPdf;

class FileGenerateService
{
    public function generateInvoice(Transaction $transaction)
    {
        // Render blade ke HTML
        $html = View::make('pdf.invoice', compact('transaction'))->render();

        // Path simpan PDF
        $invoicePath = storage_path("app/invoices/invoice_{$transaction->id}.pdf");

        // Generate PDF dari HTML
        // Generate PDF pakai Snappy
        SnappyPdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm')
            ->save($invoicePath);

        // upload file to cloud storage
        $fileUploadService = app(FileUploadService::class);
        $fileDir = 'invoices';

        $result = $fileUploadService->uploadFile($invoicePath, $fileDir);

        Invoice::create([
            'transaction_id' => $transaction->id,
            'invoice_number' => $this->generateCode('INV-'),
            'status_id' => Status::STATUS_UNPAID,
            'currency_id' => $transaction->currency_id,
            'amount' => $transaction->total_amount,
            'amount_base' => $transaction->total_amount_base,
            'invoice_date' => now(),
            'due_date' => now()->addDays(1),
            'url' => $result['fileURL'] ?? null,
        ]);

        unlink($invoicePath);

        return [
            'path' => $invoicePath,
            'url' => $result['fileURL'] ?? null,
            'filename' => $result['filePath'] ?? null,
        ];
    }

    public function generateETicket(Transaction $transaction)
    {
        // Logic to generate ticket file (e.g., PDF)
        // This is a placeholder implementation
        $ticketPath = storage_path("tickets/ticket_{$transaction->id}.pdf");

        // render blade ke HTML
        $html = View::make('pdf.ticket', compact('transaction'))->render();

        // Generate PDF dari HTML
        SnappyPdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm')
            ->save($ticketPath);

        // upload file to cloud storage
        $fileUploadService = app(FileUploadService::class);
        $fileDir = 'etickets';

        $result = $fileUploadService->uploadFile($ticketPath, $fileDir);

        ETicket::create([
            'transaction_id' => $transaction->id,
            'ticket_code' => $this->generateCode('ETK-'),
            'filename' => $result['filePath'] ?? null,
            'url' => $result['fileURL'] ?? null,
        ]);

        unlink($ticketPath);

        return [
            'path' => $ticketPath,
            'url' => $result['fileURL'] ?? null,
            'filename' => $result['filePath'] ?? null,
        ];
    }

    private function generateCode(string $prefix, int $length = 8): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $prefix . $randomString;
    }
}
