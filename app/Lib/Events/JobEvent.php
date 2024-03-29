<?php

namespace App\Lib\Events;

use App\Lib\JobMiddleware\JobChannels;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\UuidInterface;

class JobEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message = '';

    final private function __construct(public UuidInterface $jobId)
    {
    }

    public static function on(UuidInterface $jobId): static
    {
        return new static($jobId);
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
            new Channel('jobs'),
        ];
    }
}
