<?php

namespace App\Member;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'address' => $this->address,
            'zip' => $this->zip,
            'location' => $this->location,
            'send_newspaper' => $this->send_newspaper,
            'birthday' => $this->birthday->format('d.m.Y'),
            'joined_at' => $this->joined_at->format('d.m.Y'),
        ];
    }
}
