<?php

namespace App\Contribution\Requests;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\Data\MemberData;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use Lorisleiva\Actions\ActionRequest;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;

class GenerateApiRequest extends ActionRequest implements HasContributionData {
    /**
     * @return array<string, string>
     */
    public function payload(): array
    {
        return $this->input();
    }

    /**
     * @return string|array<array-key, mixed>
     */
    public function value(string $key): string|array
    {
        if (!Arr::has($this->payload(), $key)) {
            throw new RuntimeException('Wert für '.$key.' nicht gefunden.');
        }
        return data_get($this->payload(), $key);
    }

    /**
     * @return class-string<ContributionDocument>
     */
    public function type(): string
    {
        return $this->value('type');
    }

    public function dateFrom(): Carbon {
        return Carbon::parse($this->value('dateFrom'));
    }

    public function dateUntil(): Carbon {
        return Carbon::parse($this->value('dateUntil'));
    }

    public function zipLocation(): string {
        return $this->value('zipLocation');
    }

    public function eventName(): string {
        return $this->value('eventName');
    }

    public function members(): Collection {
        return MemberData::fromApi($this->value('member_data'));
    }

    public function country(): ?Country {
        return Country::where('id', $this->value('country'))->first();
    }

}
