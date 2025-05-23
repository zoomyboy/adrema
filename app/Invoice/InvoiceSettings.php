<?php

namespace App\Invoice;

use App\Setting\Contracts\Storeable;
use App\Setting\LocalSettings;
use Lorisleiva\Actions\ActionRequest;

class InvoiceSettings extends LocalSettings implements Storeable
{
    public ?string $from_long;
    public ?string $from;
    public ?string $mobile;
    public ?string $email;
    public ?string $website;
    public ?string $address;
    public ?string $place;
    public ?string $zip;
    public ?string $iban;
    public ?string $bic;
    public ?int $rememberWeeks;

    public static function group(): string
    {
        return 'bill';
    }

    /**
     * @inheritdoc
     */
    public function viewData(): array
    {
        return [
            'data' => [
                'from_long' => $this->from_long,
                'from' => $this->from,
                'mobile' => $this->mobile,
                'email' => $this->email,
                'website' => $this->website,
                'address' => $this->address,
                'place' => $this->place,
                'zip' => $this->zip,
                'iban' => $this->iban,
                'bic' => $this->bic,
                'rememberWeeks' => $this->rememberWeeks,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            'from_long' => '',
            'from' => '',
            'mobile' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'place' => '',
            'zip' => '',
            'iban' => '',
            'bic' => '',
            'rememberWeeks' => '',
        ];
    }

    public function beforeSave(ActionRequest $request): void
    {
    }

    public static function title(): string
    {
        return 'Rechnung';
    }
}
