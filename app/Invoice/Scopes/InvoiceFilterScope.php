<?php

namespace App\Invoice\Scopes;

use App\Invoice\Enums\InvoiceStatus;
use App\Invoice\Models\Invoice;
use App\Lib\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @extends Filter<Invoice>
 */
#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class InvoiceFilterScope extends Filter
{
    /**
     * @param array<int, string> $statuses
     */
    public function __construct(
        public ?array $statuses = null,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(Builder $query): Builder
    {
        $query = $query->whereIn('status',  $this->statuses);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function toDefault(): self
    {
        $this->statuses = $this->statuses === null ? InvoiceStatus::defaultVisibleValues()->toArray() : $this->statuses;
        return $this;
    }
}
