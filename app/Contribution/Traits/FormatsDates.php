<?php

namespace App\Contribution\Traits;

use Carbon\Carbon;

trait FormatsDates
{

    public function niceDateFrom(): string
    {
        return Carbon::parse($this->dateFrom)->format('d.m.Y');
    }

    public function niceDateUntil(): string
    {
        return Carbon::parse($this->dateUntil)->format('d.m.Y');
    }

    public function dateRange(): string
    {
        return implode(' - ', [$this->niceDateFrom(), $this->niceDateUntil()]);
    }
}
