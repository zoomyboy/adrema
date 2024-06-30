<?php

namespace App\Fileshare\Data;

use App\Fileshare\Models\Fileshare;
use Illuminate\Filesystem\FilesystemAdapter;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class FileshareResourceData extends Data
{

    public function __construct(public int $connectionId, public string $resource)
    {
    }

    public function getConnection(): Fileshare
    {
        return Fileshare::find($this->connectionId);
    }

    public function getStorage(): FilesystemAdapter
    {
        return $this->getConnection()->type->getFilesystem();
    }
}
