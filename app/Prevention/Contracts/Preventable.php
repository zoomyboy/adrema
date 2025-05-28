<?php

namespace App\Prevention\Contracts;

use App\Prevention\Data\PreventionData;
use Illuminate\Support\Collection;
use stdClass;

interface Preventable
{

    public function preventableSubject(): string;

    /**
     * @return Collection<int, PreventionData>
     */
    public function preventions(): Collection;

    public function getMailRecipient(): ?stdClass;
}
