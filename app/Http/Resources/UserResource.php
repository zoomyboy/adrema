<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;

/**
 * @mixin \Zoomyboy\LaravelNami\NamiUser
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->getFirstname(),
            'email' => null,
            'avatar' => [
                'src' => Storage::url('avatar.png')
            ]
        ];
    }
}
