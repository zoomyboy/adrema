<?php

namespace App\Invoice;

use App\Payment\Payment;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BillDocument extends InvoiceDocument
{

    public function getSubject(): string
    {
        return 'Rechnung';
    }

    public function view(): string
    {
        return 'tex.bill';
    }
}
