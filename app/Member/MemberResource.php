<?php

namespace App\Member;

use App\Payment\PaymentResource;
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
            'joined_at' => $this->joined_at->format('Y-m-d'),
            'joined_at_human' => $this->joined_at->format('d.m.Y'),
            'id' => $this->id,
            'subscription_id' => $this->subscription_id,
            'subscription_name' => $this->subscription_name,
            'gender_id' => $this->gender_id,
            'further_address' => $this->further_address,
            'work_phone' => $this->work_phone,
            'mobile_phone' => $this->mobile_phone,
            'main_phone' => $this->main_phone,
            'email' => $this->email,
            'email_parents' => $this->email_parents,
            'fax' => $this->fax,
            'country_id' => $this->country_id,
            'region_id' => $this->region_id,
            'nationality_id' => $this->nationality_id,
            'other_country' => $this->other_country,
            'confession_id' => $this->confession_id,
            'letter_address' => $this->letter_address,
            'bill_kind_id' => $this->bill_kind_id,
            'bill_kind_name' => optional($this->billKind)->name,
            'has_nami' => $this->nami_id !== null,
            'is_confirmed' => $this->is_confirmed,
            'children_phone' => $this->children_phone,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'pending_payment' => $this->pending_payment ? number_format($this->pending_payment / 100, 2, ',', '.').' €' : null,
            'first_activity_id' => $this->first_activity_id,
            'first_subactivity_id' => $this->first_subactivity_id,
        ];
    }
}
