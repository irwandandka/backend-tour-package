<?php

namespace App\Services;

use App\Events\TransactionOrdered;
use App\Events\TransactionPaid;
use App\Models\PaymentMethod;
use App\Models\Status;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    public function listPaymentMethods()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->select('id', 'name', 'code', 'description')
            ->get();

        return $paymentMethods;
    }

    public function setPaymentMethod(Transaction $transaction, Request $request)
    {
        return DB::transaction(function () use ($transaction, $request) {
            $transaction->load(['transactionDetails', 'status', 'currency', 'product']);
            $paymentMethod = PaymentMethod::find($request->validated('payment_method'));

            if (in_array($transaction->status_id, [Status::STATUS_COMPLETED, Status::STATUS_PAID, Status::STATUS_EXPIRED, Status::STATUS_ORDERED])) {
                throw new BadRequestHttpException('You cannot change the payment method for this transaction');
            }

            $transaction->paymentMethod()->associate($paymentMethod);
            $transaction->status()->associate(Status::where('code', 'pending')->first());
            $transaction->save();

            event(new TransactionOrdered($transaction));

            return $transaction;
        });
    }

    public function processPayment(
        Transaction $transaction,
        Request $request
    ) {
        return DB::transaction(function () use ($transaction, $request) {
            // Implement payment logic here

            $result = null;

            switch ($request->payment_method) {
                case PaymentMethod::ID_GOPAY:
                    $result = $this->payWithGopay(
                        $transaction,
                        $request
                    );
                    break;
                case PaymentMethod::ID_BANK_TRANSFER:
                    $result = $this->payWithBankTransfer(
                        $transaction,
                        $request
                    );
                    break;
                case PaymentMethod::ID_MANDIRI_VA:
                    break;
                case PaymentMethod::ID_BCA_VA:
                    break;
                    // Add other payment methods here
                default:
                    throw new \Exception('Unsupported payment method');
            }

            return $result;
        });
    }

    // skema dengan deeplink
    private function payWithGopay(Transaction $transaction, Request $request)
    {
        $orderId = $transaction->id;
        $amount = $transaction->total_amount;
        $callbackUrl = route('midtrans.callback');

        $gopay = app(GopayPaymentService::class);
        $response = $gopay->charge($orderId, $amount, $callbackUrl);
        Log::channel('transaction')->info('Midtrans RAW charge response:', $response);

        if (! isset($response['status_code']) || $response['status_code'] != '201') {
            $errorMessage = $response['status_message'] ?? 'Failed to create Midtrans transaction.';
            throw new \Exception($errorMessage);
        }

        $actions = collect($response['actions']);
        $deepLinkAction = $actions->firstWhere('name', 'deeplink-redirect');
        $deepLinkUrl = $deepLinkAction['url'] ?? null;

        if (! $deepLinkUrl) {
            throw new \Exception('GoPay deep link URL not found in Midtrans response.');
        }

        // Update paid_amount
        $transaction->paid_amount = $amount;
        $transaction->save();

        // Kembalikan URL deep link ke frontend
        return [
            'order_id' => $transaction->id,
            'status' => $response['transaction_status'] ?? 'pending',
            'deep_link_url' => $deepLinkUrl, // <-- Kirim URL ini ke frontend
        ];
    }

    // Skema dengan QRcode
    // private function payWithGopay(Transaction $transaction, Request $request)
    // {
    //     $orderId = $transaction->id;
    //     $amount = $transaction->total_amount;
    //     $callbackUrl = route('midtrans.callback');

    //     $gopay = app(GopayPaymentService::class);
    //     $response = $gopay->charge($orderId, $amount, $callbackUrl);
    //     Log::channel('transaction')->info('Midtrans RAW charge response:', $response);

    //     if (!isset($response['status_code']) || $response['status_code'] != '201') {
    //         $errorMessage = $response['status_message'] ?? 'Failed to create Midtrans transaction.';
    //         throw new \Exception($errorMessage);
    //     }

    //     // =================================================================
    //     // !! PERUBAHAN UTAMA DI SINI !!
    //     // Kita tidak lagi menggunakan actions[0], tapi mencari berdasarkan nama.
    //     // =================================================================
    //     $actions = collect($response['actions']);
    //     $qrAction = $actions->firstWhere('name', 'generate-qr-code-v2');
    //     $qrUrl = $qrAction['url'] ?? null;
    //     // =================================================================

    //     $qrBase64 = null;
    //     if ($qrUrl) {
    //         $midtransServerKey = config('midtrans.server_key');

    //         // =================================================================
    //         // !! PERBAIKAN DI SINI !!
    //         // Tambahkan header Accept: image/png
    //         // =================================================================
    //         $qrResponse = Http::withBasicAuth($midtransServerKey, '')
    //             ->withHeaders(['Accept' => 'image/png']) // <-- TAMBAHKAN HEADER INI
    //             ->get($qrUrl);
    //         // =================================================================

    //         if ($qrResponse->successful()) {
    //             $qrBase64 = 'data:image/png;base64,' . base64_encode($qrResponse->body());
    //         } else {
    //             Log::channel('transaction')->error('Gagal mengambil gambar QR Code dari Midtrans.', [
    //                 'url' => $qrUrl,
    //                 'status_code' => $qrResponse->status(),
    //                 'body' => $qrResponse->json() ?? $qrResponse->body(),
    //             ]);
    //         }
    //     }

    //     $transaction->paid_amount = $amount;
    //     $transaction->save();

    //     return [
    //         'order_id'   => $transaction->id,
    //         'status'     => $response['transaction_status'] ?? 'pending',
    //         'qr_base64'  => $qrBase64,
    //     ];
    // }

    public function handleCallbackGopay(Request $request)
    {
        $payload = $request->all();
        Log::channel('transaction')->info('Midtrans callback received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        if (! $orderId) {
            return response()->json(['message' => 'Order ID not found'], 400);
        }

        // Cari transaksi di database Anda
        $transaction = Transaction::find($orderId);
        if (! $transaction) {
            throw new NotFoundHttpException('Transaction not found in local DB');
        }

        // =================================================================
        // LANGKAH VERIFIKASI KE MIDTRANS (BAGIAN BARU & PENTING)
        // =================================================================
        try {
            $midtransServerKey = config('midtrans.server_key');
            $isProduction = config('midtrans.is_production');

            // Tentukan URL berdasarkan environment
            $statusUrl = $isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

            // Lakukan GET request ke Midtrans untuk verifikasi
            $response = Http::withBasicAuth($midtransServerKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->get($statusUrl);

            if ($response->failed()) {
                Log::channel('transaction')->error('Failed to verify transaction status to Midtrans.', [
                    'order_id' => $orderId,
                    'response_status' => $response->status(),
                    'response_body' => $response->body(),
                ]);
                throw new \Exception('Failed to verify transaction status to Midtrans.');
            }

            $midtransStatus = $response->json();
            Log::channel('transaction')->info('Midtrans verification response:', $midtransStatus);

            // Dapatkan status resmi dari hasil verifikasi
            $verifiedStatus = $midtransStatus['transaction_status'] ?? null;

            // Update status transaction HANYA JIKA statusnya berubah
            if ($transaction->status->code !== $verifiedStatus) {
                $newStatus = Status::where('code', $verifiedStatus)->first();
                if ($newStatus) {
                    $transaction->status()->associate($newStatus);
                    $transaction->save();

                    // Trigger event HANYA jika pembayaran berhasil (settlement/capture)
                    if ($verifiedStatus === 'settlement' || $verifiedStatus === 'capture') {
                        Log::channel('transaction')->info('Triggering TransactionPaid event for transaction:', ['id' => $transaction->id]);
                        event(new TransactionPaid($transaction));
                    }
                }
            }

            return response()->json(['message' => 'Callback processed successfully']);
        } catch (\Throwable $e) {
            Log::channel('transaction')->error('Error in Midtrans callback handler: '.$e->getMessage());

            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function payWithBankTransfer(
        Transaction $transaction,
        Request $request
    ) {
        // Implement bank transfer payment logic here
        $file = $request->file('proof_of_payment');

        // save to cloud storage
        $fileUploadService = app(FileUploadService::class);
        $fileDir = 'proof_of_payments';
        $result = $fileUploadService->uploadFile($file->getPathname(), $fileDir);

        // update paid_amount
        $transaction->paid_amount = $transaction->total_amount;
        $transaction->save();

        return [];
    }
}
