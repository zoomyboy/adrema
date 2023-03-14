<?php

namespace App\Contribution\Documents;

use Zoomyboy\Tex\Document;

abstract class ContributionDocument extends Document
{
    abstract public static function getName(): string;

    /**
     * @param array<string, mixed> $payload
     */
    abstract public static function fromRequest(array $payload): static;

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
}
