<?php

namespace App\Invoice\Resources;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Lib\HasMeta;
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
            'to_name' => $this->to['name'],
            'sum_human' => number_format($this->positions->sum('price') / 100, 2, ',', '') . ' €',
            'sent_at_human' => $this->sent_at?->format('d.m.Y') ?: '',
            'status' => $this->status->value,
            'via' => $this->via->value,
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
            ],
            'vias' => BillKind::forSelect(),
            'statuses' => InvoiceStatus::forSelect(),
        ];
    }
}
