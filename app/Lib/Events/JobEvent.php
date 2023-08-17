<?php

namespace App\Lib\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\UuidInterface;

class JobEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $reload = false;
    public string $message = '';

    final private function __construct(public string $channel, public UuidInterface $jobId)
    {
    }

    public static function on(string $channel, UuidInterface $jobId): static
    {
        return new static($channel, $jobId);
    }

    public function withMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function dispatch(): void
    {
        event($this);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn()
    {
        return [
            new Channel($this->channel),
            new Channel('jobs'),
        ];
    }

    public function shouldReload(): static
    {
        $this->reload = true;

        return $this;
    }
}
