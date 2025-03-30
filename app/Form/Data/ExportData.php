<?php

namespace App\Form\Data;

use App\Fileshare\Data\FileshareResourceData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\EloquentCasts\DataEloquentCast;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class ExportData extends Data
{
    public function __construct(public ?FileshareResourceData $root = null, public ?string $groupBy = null, public ?string $toGroupField = null)
    {
    }

    /**
     * @param array<int, mixed> $arguments
     * @return DataEloquentCast<self>
     */
    public static function castUsing(array $arguments): DataEloquentCast
    {
        return new DataEloquentCast(static::class, $arguments);
    }
}
