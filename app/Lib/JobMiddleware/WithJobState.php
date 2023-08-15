<?php

namespace App\Lib\JobMiddleware;

use App\Lib\Events\JobFailed;
use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use Closure;
use Lorisleiva\Actions\Decorators\JobDecorator;
use Throwable;

class WithJobState
{

    public JobStarted $beforeMessage;
    public JobFinished $afterMessage;
    public JobFailed $failedMessage;

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

    public function failed(string $message): self
    {
        $this->failedMessage = JobFailed::on($this->channel)->withMessage($message);

        return $this;
    }

    public function shouldReload(): self
    {
        $this->afterMessage->shouldReload();
        $this->failedMessage->shouldReload();

        return $this;
    }

    public function handle(JobDecorator $job, Closure $next): void
    {
        event($this->beforeMessage);

        try {
            $next($job);
        } catch (Throwable $e) {
            event($this->failedMessage);
            throw $e;
        }

        event($this->afterMessage);
    }
}
