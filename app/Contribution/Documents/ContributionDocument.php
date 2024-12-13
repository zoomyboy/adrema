<?php

namespace App\Contribution\Documents;

use Zoomyboy\Tex\Document;
use Zoomyboy\Tex\Template;

abstract class ContributionDocument extends Document
{
    private string $eventName;

    abstract public static function getName(): string;

    /**
     * @param ContributionRequestArray $request
     */
    abstract public static function fromRequest(array $request): self;

    /**
     * @param ContributionApiRequestArray $request
     */
    abstract public static function fromApiRequest(array $request): self;

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
            'members' => 'present|array|min:1',
            'members.*' => 'integer|exists:members,id',
        ];
    }

    public static function buttonName(): string
    {
        return 'Für ' . static::getName() . ' erstellen';;
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
