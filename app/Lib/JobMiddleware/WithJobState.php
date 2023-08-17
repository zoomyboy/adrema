<?php

namespace App\Lib\JobMiddleware;

use App\Lib\Events\JobFailed;
use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use Closure;
use Lorisleiva\Actions\Decorators\JobDecorator;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\UuidInterface;
use Throwable;

class WithJobState
{

    public ?JobStarted $beforeMessage = null;
    public ?JobFinished $afterMessage = null;
    public ?JobFailed $failedMessage = null;

    private function __construct(public string $channel, public UuidInterface $jobId)
    {
    }

    public static function make(string $channel, UuidInterface $jobId): self
    {
        return new self($channel, $jobId);
    }

    public function before(string $message): self
    {
        $this->beforeMessage = JobStarted::on($this->channel, $this->jobId)->withMessage($message);

        return $this;
    }

    public function after(string $message): self
    {
        $this->afterMessage = JobFinished::on($this->channel, $this->jobId)->withMessage($message);

        return $this;
    }

    public function failed(string $message): self
    {
        $this->failedMessage = JobFailed::on($this->channel, $this->jobId)->withMessage($message);

        return $this;
    }

    public function shouldReload(): self
    {
        $this->afterMessage?->shouldReload();
        $this->failedMessage?->shouldReload();

        return $this;
    }

    public function handle(JobDecorator $job, Closure $next): void
    {
        if ($this->beforeMessage) {
            event($this->beforeMessage);
        }

        try {
            $next($job);
        } catch (Throwable $e) {
            if ($this->failedMessage) {
                event($this->failedMessage);
            }
            throw $e;
        }

        if ($this->afterMessage) {
            event($this->afterMessage);
        }
    }
}
