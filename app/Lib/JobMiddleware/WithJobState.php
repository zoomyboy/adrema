<?php

namespace App\Lib\JobMiddleware;

use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use Closure;
use Lorisleiva\Actions\Decorators\JobDecorator;

class WithJobState
{

    public JobStarted $beforeMessage;
    public JobFinished $afterMessage;

    private function __construct(public string $channel)
    {
    }

    public static function make(string $channel): self
    {
        return new self($channel);
    }

    public function before(string $message): self
    {
        $this->beforeMessage = JobStarted::on($this->channel)->withMessage($message);

        return $this;
    }

    public function after(string $message): self
    {
        $this->afterMessage = JobFinished::on($this->channel)->withMessage($message);

        return $this;
    }

    public function shouldReload(): self
    {
        $this->afterMessage->shouldReload();

        return $this;
    }

    public function handle(JobDecorator $job, Closure $next): void
    {
        event($this->beforeMessage);
        $next($job);
        event($this->afterMessage);
    }
}
