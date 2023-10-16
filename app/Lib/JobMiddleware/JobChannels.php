<?php

namespace App\Lib\JobMiddleware;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<int, string>
 */
class JobChannels implements Arrayable
{

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<int, string> $channels
     */
    public function __construct(
        public array $channels = []
    ) {
    }

    public function add(string $channelName): self
    {
        $this->channels[] = $channelName;

        return $this;
    }

    public function toArray(): array
    {
        return $this->channels;
    }

    /**
     * @return array<int, Channel>
     */
    public function toBroadcast(): array
    {
        return array_map(fn ($channel) => new Channel($channel), $this->channels);
    }
}
