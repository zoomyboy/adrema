<?php

namespace App\Invoice\Resources;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Lib\HasMeta;
use App\Member\Member;
use App\Payment\Subscription;
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
            'mail_email' => $this->mail_email,
            'links' => [
                'pdf' => route('invoice.pdf', ['invoice' => $this->getModel()]),
                'rememberpdf' => route('invoice.rememberpdf', ['invoice' => $this->getModel()]),
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
                'masspdf' => route('invoice.masspdf'),
                'newInvoiceAttributes' => route('invoice.new-invoice-attributes')
            ],
            'vias' => BillKind::forSelect(),
            'statuses' => InvoiceStatus::forSelect(),
            'members' => Member::forSelect(),
            'subscriptions' => Subscription::forSelect(),
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
                'mail_email' => '',
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
