<?php

namespace App\Contribution\Requests;

use App\Contribution\Contracts\HasContributionData;
use App\Contribution\ContributionFactory;
use App\Contribution\Data\MemberData;
use App\Contribution\Documents\ContributionDocument;
use App\Country;
use Lorisleiva\Actions\ActionRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class GenerateRequest extends ActionRequest implements HasContributionData {
    /**
     * @return array<string, string>
     */
    protected function payload(): array
    {
        return json_decode(rawurldecode(base64_decode($this->input('payload', ''))), true);
    }

    public function validateContribution(): void {
        Validator::make($this->payload(), app(ContributionFactory::class)->rules($this->type()))->validate();
    }

    /**
     * @return string|array<array-key, mixed>
     */
    public function value(string $key): string|array
    {
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
        return MemberData::fromModels($this->value('members'));
    }

    public function country(): ?Country {
        return Country::where('id', $this->value('country'))->first();
    }

}
