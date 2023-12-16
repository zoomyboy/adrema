<?php

namespace App\Lib\Queue;

use App\Lib\JobMiddleware\JobChannels;
use Illuminate\Support\Str;
use App\Lib\JobMiddleware\WithJobState;

trait TracksJob
{
    abstract public function jobState(WithJobState $jobState, ...$parameters): WithJobState;

    /**
     * @param mixed $parameters
     */
    public function startJob(...$parameters): void
    {
        $jobId = Str::uuid();
        $jobState = WithJobState::make($jobId);
        tap($this->jobState(...[$jobState, ...$parameters])->beforeMessage, fn ($beforeMessage) => $beforeMessage && $beforeMessage->dispatch());;
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
        $jobState = WithJobState::make($jobId);
        $jobState = $this->jobState(...[$jobState, ...$parameters]);
        $jobState->beforeMessage = null;

        return [
            $jobState
        ];
    }
}
