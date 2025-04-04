<?php

namespace Tests\RequestFactories;

class MemberUpdateRequestFactory extends MemberRequestFactory
{
    public function noNami(): self
    {
        return $this->state(['has_nami' => false]);
    }
}
