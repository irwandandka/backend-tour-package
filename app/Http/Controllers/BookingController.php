<?php

namespace App\Http\Controllers;

use App\Http\Resources\Booking\BookingHistoryDetailResource;
use App\Http\Resources\Booking\BookingHistoryResource;
use App\Services\{BookingService, ErrorHandler};
use Exception;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    use ValidatesRequests;

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
        Request $request
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
        string $id
    ) {
        try {
            $data = $this->bookingService->getTransaction($id);

            return response()->json([
                'status' => 'success',
                'data' => new BookingHistoryDetailResource($data),
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function cancel(
        string $id
    ) {
        try {
            return DB::transaction(function () use ($id) {
                $data = $this->bookingService->cancelBooking($id);

                return response()->json(new BookingHistoryDetailResource($data));
            });
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function update(
        Request $request,
        string $id
    ) {
        try {
            $result = $this->bookingService->updateBooking($id, $request);

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
        Request $request,
        string $id
    ) {
        try {
            $result = $this->bookingService->submitReview($request, $id);

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
