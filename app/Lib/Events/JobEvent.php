<?php

namespace App\Lib\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $reload = false;
    public string $message = '';

    final private function __construct(public string $channel)
    {
    }

    public static function on(string $channel): static
    {
        return new static($channel);
    }

    public function withMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel($this->channel);
    }

    public function shouldReload(): static
    {
        $this->reload = true;

        return $this;
    }
}
