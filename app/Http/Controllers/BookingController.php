<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\SubmitReviewRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\Booking\BookingHistoryDetailResource;
use App\Http\Resources\Booking\BookingHistoryResource;
use App\Models\Transaction;
use App\Services\{BookingService, ErrorHandler};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookingController extends Controller
{
    use AuthorizesRequests;

    private $errorHandler;
    private $bookingService;

    public function __construct(
        ErrorHandler $errorHandler,
        BookingService $bookingService
    ) {
        $this->errorHandler = $errorHandler;
        $this->bookingService = $bookingService;
    }

    public function store(
        CreateBookingRequest $request
    ) {
        try {
            $result = $this->bookingService->createTransaction($request);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking success',
                'data' => new BookingHistoryDetailResource($result),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function show(
        Transaction $transaction
    ) {
        try {
            $this->authorize('view', $transaction);

            $data = $this->bookingService->getTransaction($transaction);

            return response()->json([
                'status' => 'success',
                'data' => new BookingHistoryDetailResource($data),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function cancel(
        Transaction $transaction
    ) {
        try {
            $this->authorize('cancel', $transaction);

            return DB::transaction(function () use ($transaction) {
                $data = $this->bookingService->cancelBooking($transaction);

                return response()->json(new BookingHistoryDetailResource($data));
            });
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function update(
        UpdateBookingRequest $request,
        Transaction $transaction
    ) {
        try {
            $this->authorize('update', $transaction);

            $result = $this->bookingService->updateBooking($transaction, $request);

            return response()->json([
                'status' => 'success',
                'data' => new BookingHistoryDetailResource($result),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function history(
        Request $request
    ) {
        try {
            $bookings = $this->bookingService->getHistory($request);

            return response()->json([
                'status' => 'success',
                'data' => BookingHistoryResource::collection($bookings),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function submitReview(
        SubmitReviewRequest $request,
        Transaction $transaction
    ) {
        try {
            $this->authorize('review', $transaction);

            $result = $this->bookingService->submitReview($request, $transaction);

            return response()->json([
                'status' => 'success',
                'message' => 'Review submitted successfully',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}
