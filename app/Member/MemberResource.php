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
            'birthday' => $this->birthday->format('Y-m-d'),
            'birthday_human' => $this->birthday->format('d.m.Y'),
            'joined_at' => $this->joined_at->format('d.m.Y'),
            'id' => $this->id,
            'gender_id' => $this->gender_id,
            'further_address' => $this->further_address,
            'work_phone' => $this->work_phone,
            'mobile_phone' => $this->mobile_phone,
            'main_phone' => $this->main_phone,
            'email' => $this->email,
            'email_parents' => $this->email_parents,
            'fax' => $this->fax,
            'nami_id' => $this->nami_id,
            'country_id' => $this->country_id,
            'region_id' => $this->region_id,
            'nationality_id' => $this->nationality_id,
            'other_country' => $this->other_country,
            'confession_id' => $this->confession_id,
        ];
    }
}
