<?php

namespace App\Lib\Events;

use App\Lib\JobMiddleware\JobChannels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReloadTriggered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    final private function __construct(public JobChannels $channels)
    {
    }

    public static function on(JobChannels $channels): self
    {
        return new static($channels);
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
        return $this->channels->toBroadcast();
    }
}
