<?php

namespace App\Invoice\Actions;

use App\Invoice\BillKind;
use App\Invoice\Enums\InvoiceStatus;
use Illuminate\Validation\Rule;

trait HasValidation
{
    /**
     * @return array<string, string|array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:255', Rule::in(InvoiceStatus::values())],
            'via' => ['required', 'string', 'max:255', Rule::in(BillKind::values())],
            'usage' => 'required|max:255|string',
            'mail_name' => 'nullable|string|max:255',
            'mail_email' => 'nullable|string|max:255|email',
            'to' => 'array',
            'to.address' => 'required|string|max:255',
            'to.location' => 'required|string|max:255',
            'to.zip' => 'required|string|max:255',
            'to.name' => 'required|string|max:255',
            'greeting' => 'required|string|max:255',
            'positions' => 'array',
            'positions.*.description' => 'required|string|max:300',
            'positions.*.price' => 'required|integer|min:0',
            'positions.*.member_id' => 'required|exists:members,id',
            'positions.*.id' => 'present|nullable|exists:invoice_positions,id',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'to.address' => 'Adresse',
            'to.name' => 'Name',
            'to.zip' => 'PLZ',
            'to.location' => 'Ort',
            'status' => 'Status',
            'via' => 'Rechnungsweg',
            'usage' => 'Verwendungszweck',
        ];
    }
}
