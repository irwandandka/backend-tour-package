<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    // Resource implementation here

    public function toArray(
        $request
    ): array {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => Carbon::parse($this->created_at)->format('l jS F Y'),
            'profile_picture_url' => $this->profile_picture_url,
            'username' => $this->username,
            'phone' => $this->phone,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'email_verified_at' => $this->email_verified_at,
            'country' => $this->country ? $this->country->only('id', 'name') : null,
            'city' => $this->city ? $this->city->only('id', 'name') : null,
        ];
    }
}
