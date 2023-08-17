<?php

namespace App\Lib\Queue;

use Illuminate\Support\Str;
use App\Lib\JobMiddleware\WithJobState;

trait TracksJob
{
    abstract public function jobState(WithJobState $jobState, ...$parameters): WithJobState;
    abstract public function jobChannel(): string;

    /**
     * @param mixed $parameters
     */
    public function startJob(...$parameters): void
    {
        $jobId = Str::uuid();
        $jobState = WithJobState::make($this->jobChannel(), $jobId);
        $this->jobState(...[$jobState, ...$parameters])->beforeMessage->dispatch();
        $parameters[] = $jobId;
        static::dispatch(...$parameters);
    }

    /**
     * @param mixed $parameters
     *
     * @return array<int, object>
     */
    public function getJobMiddleware(...$parameters): array
    {
        $jobId = array_pop($parameters);
        $jobState = WithJobState::make($this->jobChannel(), $jobId);
        $jobState = $this->jobState(...[$jobState, ...$parameters]);
        $jobState->beforeMessage = null;

        return [
            $jobState
        ];
    }
}
