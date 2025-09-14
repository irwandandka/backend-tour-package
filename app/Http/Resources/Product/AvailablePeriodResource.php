<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AvailablePeriodResource extends JsonResource
{
    // Resource implementation here

    public function toArray(
        $request
    ): array {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'status' => $this->status,
            'product' => $this->product,
            'image' => $this->image,
            'total_amount' => $this->total_amount,
            'total_amount_base' => $this->total_amount_base,
            'booking_date' => $this->booking_date,
            'notes' => $this->notes,
        ];
    }
}
