<?php

namespace App\Invoice\Scopes;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Lib\Filter;
use App\Lib\ScoutFilter;
use Laravel\Scout\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends ScoutFilter<Invoice>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class InvoiceFilterScope extends ScoutFilter
{
    /**
     * @param array<int, string> $statuses
     */
    public function __construct(
        public ?array $statuses = null,
        public ?string $search = null
    ) {
        $this->statuses = $this->statuses === null ? InvoiceStatus::defaultVisibleValues()->toArray() : $this->statuses;
    }

    /**
     * @inheritdoc
     */
    public function getQuery(): Builder
    {
        $this->search = $this->search ?: '';

        return Invoice::search($this->search, function ($engine, string $query, array $options) {
            if (empty($this->statuses)) {
                $filter = 'status = "asa6aeruuni4BahC7Wei6ahm1"';
            } else {
                $filter = collect($this->statuses)->map(fn (string $status) => "status = \"$status\"")->join(' OR ');
            }
            return $engine->search($query, [...$options, 'filter' => $filter]);
        });
    }
}
