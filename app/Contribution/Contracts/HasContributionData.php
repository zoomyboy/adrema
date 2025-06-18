<?php

namespace App\Contribution\Contracts;

use App\Contribution\Data\MemberData;
use Carbon\Carbon;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use Illuminate\Support\Collection;

interface HasContributionData {

    public function dateFrom(): Carbon;
    public function dateUntil(): Carbon;
    public function zipLocation(): string;
    public function eventName(): string;
    /**
     * @return class-string<ContributionDocument>
     */
    public function type(): string;

    /**
     * @return Collection<int, MemberData>
     */
    public function members(): Collection;

    public function country(): ?Country;
}
