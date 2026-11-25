<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;

class UserResource extends ApiResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var User
         */
        $user = $this->resource;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'is_demo' => $user->is_demo,
            'created_at' => $this->formatDate($user->created_at),
        ];
    }
}
