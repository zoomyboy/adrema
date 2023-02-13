<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => [
                'src' => Storage::url('avatar.png'),
            ],
        ];
    }
}
