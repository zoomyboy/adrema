<?php

namespace App\Home;

use App\Efz\EfzPendingBlock;
use App\Home\Blocks\Block;
use App\Membership\AgeGroupCountBlock;
use App\Membership\TestersBlock;
use App\Payment\MemberPaymentBlock;

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
