<?php

namespace App\Form\Data;

use App\Fileshare\Data\FileshareResourceData;
use App\Form\Fields\Field;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class ExportData extends Data
{
    public function __construct(public ?FileshareResourceData $root = null, public ?string $groupBy = null, public ?string $toGroupField = null)
    {
    }
}
