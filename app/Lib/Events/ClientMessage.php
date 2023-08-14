<?php

namespace App\Lib\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientMessage implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $reload = false;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public string $message)
    {
    }

    public static function make(string $message): self
    {
        return new static($message);
    }

    public function shouldReload(): self
    {
        $this->reload = true;

        return $this;
    }

    public function dispatch(): void
    {
        event($this);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('jobs');
    }
}
