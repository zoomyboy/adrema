<?php

namespace App\Invoice\Resources;

use App\Invoice\Models\InvoicePosition;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InvoicePosition
 */
class InvoicePositionResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'member_id' => $this->member_id,
            'description' => $this->description,
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'price_human' => number_format($this->price / 100, 2, ',', '') . ' €',
        ];
    }
}
