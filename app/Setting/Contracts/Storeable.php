<?php

namespace App\Setting\Contracts;

use App\Setting\LocalSettings;
use Lorisleiva\Actions\ActionRequest;
use Spatie\LaravelSettings\Settings;

/**
 * @mixin LocalSettings
 */
interface Storeable
{
    public function url(): string;

    /**
     * @param array<string, mixed> $input
     */
    public function fill(array $input): Settings;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array;

    public function beforeSave(ActionRequest $request): void;
}
