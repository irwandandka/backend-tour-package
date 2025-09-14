<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image,
            "min_adult" => $this->min_adult,
            "max_adult" => $this->max_adult,
            "max_pax" => $this->max_pax,
            "allotment" => $this->allotment,
            "pricing" => $this->pricing,
        ];
    }
}
