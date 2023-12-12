<?php

namespace App\Invoice\Actions;

use App\Invoice\Enums\InvoiceStatus;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Invoice\Models\Invoice;
use Illuminate\Validation\Rule;

class InvoiceStoreAction
{
    use AsAction;

    /**
     * @return array<string, string|array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:255', Rule::in(InvoiceStatus::values())],
            'to' => 'array',
            'to.address' => 'required|string|max:255',
            'to.location' => 'required|string|max:255',
            'to.zip' => 'required|string|max:255',
            'to.name' => 'required|string|max:255',
            'greeting' => 'required|string|max:255',
            'intro' => 'required|string',
            'outro' => 'required|string',
            'positions' => 'array',
            'positions.*.description' => 'required|string|max:300',
            'positions.*.price' => 'required|integer|min:0',
            'positions.*.member_id' => 'required|exists:members,id',
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
        ];
    }

    public function handle(ActionRequest $request): void
    {
        $invoice = Invoice::create($request->safe()->except('positions'));

        foreach ($request->validated('positions') as $position) {
            $invoice->positions()->create($position);
        }
    }
}
