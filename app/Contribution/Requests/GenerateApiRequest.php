<?php

namespace App\Contribution\Requests;

use App\Contribution\Data\MemberData;
use Illuminate\Support\Collection;

class GenerateApiRequest extends GenerateRequest {
    /**
     * @return array<string, string>
     */
    public function payload(): array
    {
        return $this->input();
    }

    public function validateContribution(): void {
    }

    public function members(): Collection {
        return MemberData::fromApi($this->value('members'));
    }

}
