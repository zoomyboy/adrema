<?php

namespace App\Contribution\Documents;

use App\Contribution\Contracts\HasContributionData;
use App\Form\Enums\SpecialType;
use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Template;

abstract class ContributionDocument extends Document
{
    private string $eventName;

    abstract public static function getName(): string;

    abstract public static function fromPayload(HasContributionData $request): self;

    /**
     * @return array<int, SpecialType>
     */
    abstract public static function requiredFormSpecialTypes(): array;

    /**
     * @return array<string, mixed>
     */
    abstract public static function rules(): array;

    /**
     * @return array<string, mixed>
     */
    public static function globalRules(): array
    {
        return [
            'eventName' => 'required|string',
            'members' => 'present|array|min:1',
            'members.*' => 'integer|exists:members,id',
        ];
    }

    public function setEventName(string $eventName): void
    {
        $this->eventName = $eventName;
    }

    public function basename(): string
    {
        return str('Zuschüsse ')->append($this->getName())->append(' ')->append($this->eventName)->slug();
    }

    public function template(): Template
    {
        return Template::make('tex.templates.contribution');
    }

    public function view(): string
    {
        return 'tex.contribution.' . str(class_basename(static::class))->replace('Document', '')->kebab()->toString();
    }
}
