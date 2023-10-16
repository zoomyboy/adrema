<?php

namespace App\Lib\JobMiddleware;

use App\Lib\Events\JobFailed;
use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use App\Lib\Events\ReloadTriggered;
use Closure;
use Lorisleiva\Actions\Decorators\JobDecorator;
use Ramsey\Uuid\UuidInterface;
use Throwable;

class WithJobState
{

    public ?JobStarted $beforeMessage = null;
    public ?JobFinished $afterMessage = null;
    public ?JobFailed $failedMessage = null;
    public ?ReloadTriggered $reloadAfter = null;

    private function __construct(public UuidInterface $jobId)
    {
    }

    public static function make(UuidInterface $jobId): self
    {
        return new self($jobId);
    }

    public function before(string $message): self
    {
        $this->beforeMessage = JobStarted::on($this->jobId)->withMessage($message);

        return $this;
    }

    public function after(string $message): self
    {
        $this->afterMessage = JobFinished::on($this->jobId)->withMessage($message);

        return $this;
    }

    public function failed(string $message): self
    {
        $this->failedMessage = JobFailed::on($this->jobId)->withMessage($message);

        return $this;
    }

    public function shouldReload(JobChannels $channels): self
    {
        $this->reloadAfter = ReloadTriggered::on($channels);

        return $this;
    }

    public function handle(JobDecorator $job, Closure $next): void
    {
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

        if ($this->reloadAfter) {
            event($this->reloadAfter);
        }
    }
}
