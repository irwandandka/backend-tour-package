<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    // Resource implementation here

    public function toArray(
        $request
    ): array {
        $product = $this->resource;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'image' => $product->thumbnail_image,
            'duration' => $product->duration,
            'price' => formatCurrency($product->price, $product->currency),
            'rating' => round($product->reviews->avg('rating'), 1),
            'location' => $product->city->name.', '.$product->city->country->name,
            'itineraries' => $product->itineraries,
            'reviews' => $product->reviews,
        ];
    }
}
