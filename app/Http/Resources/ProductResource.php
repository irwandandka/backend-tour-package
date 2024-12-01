<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Define allowed fields and relations
        $allowedFields = ['id', 'name', 'slug', 'duration', 'description', 'price', 'thumbnail_image', 'capacity', 'date_from', 'date_until'];
        $allowedRelations = ['user', 'city', 'status', 'category'];

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
                // Dynamically determine whether the relation is one-to-many or belongsTo
                $resourceClassName = 'App\Http\Resources\\' . ucwords($relation) . 'Resource';

                if ($this->relationLoaded($relation)) {
                    // Check the type of relationship dynamically
                    if (method_exists($this->$relation(), 'getForeignKeyName')) {
                        // If it has a foreign key, it's a belongsTo or one-to-one relation
                        $response[$relation] = new $resourceClassName($this->$relation);
                    } else {
                        // Otherwise, it's a collection (e.g., hasMany or belongsToMany)
                        $response[$relation] = $resourceClassName::collection($this->$relation);
                    }
                }
            }
        }

        return $response;
    }
}
