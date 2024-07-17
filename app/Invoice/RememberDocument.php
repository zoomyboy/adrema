<?php

namespace App\Invoice;

use App\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RememberDocument extends InvoiceDocument
{

    public function getSubject(): string
    {
        return 'Zahlungserinnerung';
    }

    public function view(): string
    {
        return 'tex.invoice.remember';
    }
}
