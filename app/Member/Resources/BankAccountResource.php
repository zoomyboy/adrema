<?php

namespace App\Member\Resources;

use App\Member\BankAccount;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BankAccount
 */
class BankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<string, int|string>
     */
    public function toArray($request)
    {
        return [
            'iban' => $this->iban,
            'bic' => $this->bic,
            'blz' => $this->blz,
            'bank_name' => $this->bank_name,
            'person' => $this->person,
            'account_number' => $this->account_number,
        ];
    }
}
