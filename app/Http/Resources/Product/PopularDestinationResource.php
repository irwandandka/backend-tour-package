<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class PopularDestinationResource extends JsonResource
{
    // Resource implementation here

    public function toArray(
        $request
    ): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => formatCurrency($this->price, $this->currency),
            'location' => $this->location,
            'image' => $this->thumbnail_image,
            'rating' => number_format($this->reviews_avg_rating, 1),
        ];
    }
}
