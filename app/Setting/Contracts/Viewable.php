<?php

namespace App\Setting\Contracts;

interface Viewable
{
    public static function url(): string;

    public static function title(): string;

    public static function group(): string;

    /**
     * @return array<string, mixed>
     */
    public function viewData(): array;
}
