<?php

namespace App\Lib\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Succeeded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    final private function __construct(public string $message)
    {
    }

    public static function message(string $message): self
    {
        return new self($message);
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
            new Channel('jobs'),
        ];
    }
}
