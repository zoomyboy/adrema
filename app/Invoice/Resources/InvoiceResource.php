<?php

namespace App\Invoice\Resources;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Lib\HasMeta;
use App\Member\Member;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Invoice
 */
class InvoiceResource extends JsonResource
{

    use HasMeta;

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
            'to' => $this->to,
            'sum_human' => number_format($this->positions->sum('price') / 100, 2, ',', '') . ' €',
            'sent_at_human' => $this->sent_at?->format('d.m.Y') ?: '',
            'status' => $this->status->value,
            'via' => $this->via->value,
            'positions' => InvoicePositionResource::collection($this->whenLoaded('positions')),
            'greeting' => $this->greeting,
            'usage' => $this->usage,
            'links' => [
                'update' => route('invoice.update', ['invoice' => $this->getModel()]),
                'destroy' => route('invoice.destroy', ['invoice' => $this->getModel()]),
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function meta(): array
    {
        return [
            'links' => [
                'mass-store' => route('invoice.mass-store'),
                'store' => route('invoice.store'),
            ],
            'vias' => BillKind::forSelect(),
            'statuses' => InvoiceStatus::forSelect(),
            'members' => Member::forSelect(),
            'default' => [
                'to' => [
                    'name' => '',
                    'address' => '',
                    'zip' => '',
                    'location' => '',
                ],
                'positions' => [],
                'greeting' => '',
                'status' => InvoiceStatus::NEW->value,
                'via' => null,
                'usage' => '',
            ],
            'default_position' => [
                'id' => null,
                'price' => 0,
                'description' => '',
                'member_id' => null,
            ]
        ];
    }
}
