<?php

namespace App\Http\Resources;

use App\Traits\JsonResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    use JsonResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Define allowed fields and relations
        $allowedFields = ['id', 'name', 'iso_code', 'phone_code', 'created_at'];
        $allowedRelations = ['cities'];

        // Get fields and relations from the request
        $fields = explode(',', $request->query('fields', ''));
        $relations = explode(',', $request->query('relations', ''));

        // Initialize response array
        $response = [];

        // Include allowed fields
        foreach ($allowedFields as $field) {
            if (in_array($field, $fields) || empty($fields)) {
                if ($field == 'created_at') {
                    $response[$field] = $this->formatCreatedAt($this->$field);
                } else {
                    $response[$field] = $this->$field;
                }
            }
        }

        // Include allowed relations
        foreach ($allowedRelations as $relation) {
            if (in_array($relation, $relations) || empty($relations)) {
                $response[$relation] = CityResource::collection($this->whenLoaded($relation));
            }
        }

        return $response;
    }
}
