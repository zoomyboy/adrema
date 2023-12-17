<?php

namespace App\Dashboard;

use App\Dashboard\Blocks\Block;
use App\Efz\EfzPendingBlock;
use App\Invoice\MemberPaymentBlock;
use App\Member\PsPendingBlock;
use App\Membership\AgeGroupCountBlock;
use App\Membership\TestersBlock;

class DashboardFactory
{
    /**
     * @var array<int, class-string<Block>>
     */
    private array $blocks = [
        AgeGroupCountBlock::class,
        MemberPaymentBlock::class,
        TestersBlock::class,
        EfzPendingBlock::class,
        PsPendingBlock::class,
    ];

    /**
     * @return array<array-key, mixed>
     */
    public function render(): array
    {
        return collect($this->blocks)->map(fn ($block): array => app($block)->render())->toArray();
    }

    /**
     * @param class-string<Block> $block
     */
    public function register(string $block): self
    {
        $this->blocks[] = $block;

        return $this;
    }

    public function purge(): self
    {
        $this->blocks = [];

        return $this;
    }
}
