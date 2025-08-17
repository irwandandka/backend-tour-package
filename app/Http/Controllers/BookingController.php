<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Transaction;
use App\Services\BookingService;
use App\Services\ErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    use ValidatesRequests;

    private $errorHandler;
    private $bookingService;

    public function __construct()
    {
        $this->errorHandler = new ErrorHandler;
        $this->bookingService = new BookingService;
    }

    /**
     * @OA\Post(
     *      path="/api/booking",
     *      tags={"Booking"},
     *      summary="Booking a product",
     *      description="Booking a product",
     *      operationId="booking",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"currency", "product_id", "date_from", "date_to", "product_details"},
     *              @OA\Property(property="currency", type="string", example="USD"),
     *              @OA\Property(property="product_id", type="integer", example=1),
     *              @OA\Property(property="date_from", type="string", format="date", example="2023-10-01"),
     *              @OA\Property(property="date_to", type="string", format="date", example="2023-10-02"),
     *              @OA\Property(property="product_details", type="array", @OA\Items(
     *              @OA\Property(property="product_detail", type="integer", example=1),
     *              @OA\Property(property="quantity", type="integer", example=1),
     *              @OA\Property(property="quantity_adult", type="integer", example=1),
     *              @OA\Property(property="quantity_child", type="integer", example=0),
     *              @OA\Property(property="quantity_infant", type="integer", example=0),
     *              @OA\Property(property="quantity_senior", type="integer", example=0),
     *          )),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Booking success",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="Booking success"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="string", example="1"),
     *                  @OA\Property(property="code", type="string", example="TRX-20231001-0001"),
     *                  @OA\Property(property="status", type="string", example="Pending"),
     *                  @OA\Property(property="product", type="string", example="Product Name"),
     *                  @OA\Property(property="quantity", type="integer", example=1),
     *                  @OA\Property(property="sales_total", type="number", format="float", example=100.00),
     *                  @OA\Property(property="sales_total_base", type="number", format="float", example=100.00),
     *                  @OA\Property(property="booking_date", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                  @OA\Property(property="notes", type="string", example="Booking a product"),
     *                  @OA\Property(property="transaction_details", type="array", @OA\Items(
     *                  @OA\Property(property="product_detail_name", type="string", example="Product Detail Name"),
     *                  @OA\Property(property="product_detail_image", type="string", example="https://example.com/image.jpg"),
     *                  @OA\Property(property="quantity_adult", type="integer", example=1),
     *                  @OA\Property(property="quantity_child", type="integer", example=0),
     *                  @OA\Property(property="quantity_infant", type="integer", example=0),
     *                  @OA\Property(property="quantity_senior", type="integer", example=0),
     *                  @OA\Property(property="sales_adult", type="number", format="float", example=50.00),
     *                  @OA\Property(property="sales_child", type="number", format="float", example=0.00),
     *                  @OA\Property(property="sales_infant", type="number", format="float", example=0.00),
     *                  @OA\Property(property="sales_senior", type="number", format="float", example=0.00),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="currency", type="array",
     *                      @OA\Items(type="string", example="The selected currency is invalid.")
     *                  ),
     *                  @OA\Property(property="product_id", type="array",
     *                      @OA\Items(type="string", example="The selected product id is invalid.")
     *                  ),
     *                  @OA\Property(property="date_from", type="array",
     *                      @OA\Items(type="string", example="The date from field is required.")
     *                  ),
     *                  @OA\Property(property="date_to", type="array",
     *                      @OA\Items(type="string", example="The date to field is required.")
     *                  ),
     *                  @OA\Property(property="product_details", type="array",
     *                      @OA\Items(type="string", example="The product details field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.product_detail", type="array",
     *                      @OA\Items(type="string", example="The product detail field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.quantity", type="array",
     *                      @OA\Items(type="string", example="The quantity field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.quantity_adult", type="array",
     *                      @OA\Items(type="string", example="The quantity adult field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.quantity_child", type="array",
     *                      @OA\Items(type="string", example="The quantity child field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.quantity_infant", type="array",
     *                      @OA\Items(type="string", example="The quantity infant field is required.")
     *                  ),
     *                  @OA\Property(property="product_details.*.quantity_senior", type="array",
     *                      @OA\Items(type="string", example="The quantity senior field is required.")
     *                  ),
     *              ),
     *          ),
     *          @OA\Response(
     *              response=500,
     *              description="Internal server error",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Internal server error"),
     *                  @OA\Property(property="errors", type="string", example="Error message")
     *              ),
     *          ),
     *          @OA\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Unauthorized"),
     *                  @OA\Property(property="errors", type="string", example="Unauthorized")
     *              ),
     *          ),
     *          @OA\Response(
     *              response=403,
     *              description="Forbidden",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Forbidden"),
     *                  @OA\Property(property="errors", type="string", example="Forbidden")
     *              ),
     *          ),
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $user = auth('api')->user();

            $validated = $request->validate([
                'currency' => 'required',
                'product_id' => 'required|exists:products,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date',
                'product_details' => 'required|array|min:1',
                'product_details.*.product_detail' => 'required|exists:product_details,id',
                'product_details.*.quantity' => 'required|integer|min:1',
                'product_details.*.quantity_adult' => 'required|integer|min:0',
                'product_details.*.quantity_child' => 'required|integer|min:0',
                'product_details.*.quantity_infant' => 'required|integer|min:0',
                'product_details.*.quantity_senior' => 'required|integer|min:0',
            ]);

            $result = DB::transaction(function () use ($validated, $user) {
                $transaction = $this->bookingService->createTransaction($validated, $user);

                return $transaction;
            });

            // Logic to book a product
            return response()->json([
                'status' => 'success',
                'message' => 'Booking success',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/booking/{id}",
     *      tags={"Booking"},
     *      summary="Get booking detail",
     *      description="Get booking detail",
     *      operationId="getBookingDetail",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string"),
     *              description="Booking ID"
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Booking detail",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="success"),
     *                  @OA\Property(property="message", type="string", example="Transaction detail"),
     *                  @OA\Property(property="data", type="object",
     *                      @OA\Property(property="id", type="string", example="1"),
     *                      @OA\Property(property="code", type="string", example="TRX-20231001-0001"),
     *                      @OA\Property(property="status", type="string", example="Pending"),
     *                      @OA\Property(property="product", type="string", example="Product Name"),
     *                      @OA\Property(property="quantity", type="integer", example=1),
     *                      @OA\Property(property="sales_total", type="number", format="float", example=100.00),
     *                      @OA\Property(property="sales_total_base", type="number", format="float", example=100.00),
     *                      @OA\Property(property="booking_date", type="string", format="date-time", example="2023-10-01T00:00:00Z"),
     *                      @OA\Property(property="notes", type="string", example="Booking a product"),
     *                      @OA\Property(property="transaction_details", type="array", @OA\Items(
     *                          @OA\Property(property="id", type="string", example="1"),
     *                          @OA\Property(property="product_detail_name", type="string", example="Product Detail Name"),
     *                          @OA\Property(property="product_detail_image", type="string", example="https://example.com/image.jpg"),
     *                          @OA\Property(property="quantity_adult", type="integer", example=1),
     *                          @OA\Property(property="quantity_child", type="integer", example=0),
     *                          @OA\Property(property="quantity_infant", type="integer", example=0),
     *                          @OA\Property(property="quantity_senior", type="integer", example=0),
     *                          @OA\Property(property="sales_adult", type="number", format="float", example=50.00),
     *                          @OA\Property(property="sales_child", type="number", format="float", example=0.00),
     *                          @OA\Property(property="sales_infant", type="number", format="float", example=0.00),
     *                          @OA\Property(property="sales_senior", type="number", format="float", example=0.00),
     *                      )),
     *                  ),
     *              ),
     *          ),
     *          @OA\Response(
     *              response=404,
     *              description="Transaction not found",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Transaction not found"),
     *                  @OA\Property(property="errors", type="string", example="Transaction not found")
     *              ),
     *          ),
     *          @OA\Response(
     *              response=500,
     *              description="Internal server error",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Internal server error"),
     *                  @OA\Property(property="errors", type="string", example="Error message")
     *              ),
     *          ),
     *          @OA\Response(
     *              response=401,
     *              description="Unauthorized",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Unauthorized"),
     *                  @OA\Property(property="errors", type="string", example="Unauthorized")
     *              ),
     *          ),
     *          @OA\Response(
     *              response=403,
     *              description="Forbidden",
     *              @OA\JsonContent(
     *                  @OA\Property(property="status", type="string", example="error"),
     *                  @OA\Property(property="message", type="string", example="Forbidden"),
     *                  @OA\Property(property="errors", type="string", example="Forbidden")
     *              ),
     *          ),
     *      )
     * )
     */
    public function show($id)
    {
        try {
            $transaction = Transaction::with(
                [
                    "product",
                    "status",
                    "transactionDetails",
                    "transactionDetails.product",
                    "transactionDetails.productDetail",
                ]
            )
                ->where('id', $id)
                ->first();

            $data = [
                "id" => $transaction->id,
                "code" => $transaction->code,
                "status" => $transaction->status->name,
                "product" => $transaction->product->name,
                "quantity" => $transaction->quantity,
                "sales_total" => $transaction->sales_total,
                "sales_total_base" => $transaction->sales_total_base,
                "booking_date" => Carbon::parse($transaction->booking_date)->format("l, jS F Y"),
                "notes" => $transaction->notes,
                "transaction_details" => $transaction
                    ->transactionDetails
                    ->map(function ($detail) {
                        return [
                            "product_detail_name" => $detail->productDetail->name_en,
                            "product_detail_image" => $detail->productDetail->activity_image,
                            "quantity_adult" => $detail->quantity_adult,
                            "quantity_child" => $detail->quantity_child,
                            "quantity_infant" => $detail->quantity_infant,
                            "quantity_senior" => $detail->quantity_senior,
                            "sales_adult" => $detail->sales_adult,
                            "sales_child" => $detail->sales_child,
                            "sales_infant" => $detail->sales_infant,
                            "sales_senior" => $detail->sales_senior,
                        ];
                    }),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction detail',
                'data' => $data,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/booking/{id}/cancel",
     *    tags={"Booking"},
     *    summary="Cancel booking",
     *   description="Cancel booking",
     *   operationId="cancelBooking",
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *       name="id",
     *      in="path",
     *      required=true,
     *     @OA\Schema(type="string"),
     *     description="Booking ID"
     *    ),
     *   @OA\Response(
     *       response=200,
     *      description="Booking canceled",
     *     @OA\JsonContent(
     *           @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="message", type="string", example="Booking canceled"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=404,
     *         description="Transaction not found",
     *        @OA\JsonContent(
     *           @OA\Property(property="status", type="string", example="error"),
     *          @OA\Property(property="message", type="string", example="Transaction not found"),
     *         @OA\Property(property="errors", type="string", example="Transaction not found")
     *        ),
     *     ),
     *    @OA\Response(
     *         response=500,
     *        description="Internal server error",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="string", example="error"),
     *         @OA\Property(property="message", type="string", example="Internal server error"),
     *        @OA\Property(property="errors", type="string", example="Error message")
     *       ),
     *     ),
     *   @OA\Response(
     *        response=401,
     *       description="Unauthorized",
     *      @OA\JsonContent(
     *         @OA\Property(property="status", type="string", example="error"),
     *        @OA\Property(property="message", type="string", example="Unauthorized"),
     *       @OA\Property(property="errors", type="string", example="Unauthorized")
     *      ),
     *   )
     */
    public function cancel($id)
    {
        try {
            $transaction = Transaction::find($id);

            $statusCancel = Status::where('code', 'cancel')->first();

            $transaction->status_id = 3;
            $transaction->save();

            // Logic to cancel a booking
            return response()->json([
                'status' => 'success',
                'message' => 'Booking canceled',
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'required|string',
                'postal_code' => 'required|string',
                'passengers' => 'required|array',
                'passengers.*.first_name' => 'required|string',
                'passengers.*.last_name' => 'required|string',
                'passengers.*.title' => 'required|title|string',
                'passengers.*.nationality' => 'nationality|string|nullable',
                'passengers.*.passport_number' => 'string|nullable|unique:passengers,passport_number',
                'passengers.*.passport_expiry_date' => 'string|nullable|date_format:Y-m-d',
                'passengers.*.passport_issue_date' => 'string|nullable|date_format:Y-m-d',
                'passengers.*.passport_issue_country' => 'string|nullable',
                'passengers.*.birth_place' => 'string|nullable',
                'passengers.*.birth_date' => 'string|nullable|date_format:Y-m-d',
            ]);

            $result = DB::transaction(function () use ($validated, $id) {
                $transaction = Transaction::find($id);

                $transaction->customer_name = $validated['name'];
                $transaction->customer_email = $validated['email'];
                $transaction->customer_phone = $validated['phone'];
                $transaction->address = $validated['address'];
                $transaction->postal_code = $validated['postal_code'];
                $transaction->save();

                $paramPassengers = $validated['passengers'];

                $paramPassengers = array_map(function ($passenger) use ($transaction) {
                    return array_merge($passenger, [
                        'id' => Str::uuid(),
                        'created_at' => Carbon::now(),
                        'transaction_id' => $transaction->id
                    ]);
                }, $paramPassengers);

                Passenger::insert($paramPassengers);

                return $transaction->only(
                    ['id', 'code', 'quantity', 'total_amount', 'booking_date', 'notes']
                );
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Passenger detail updated',
                'data' => $result,
            ]);
        } catch (Throwable $e) {
            return $this->errorHandler->handle($e);
        }
    }
}
