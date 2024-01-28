<?php

namespace Tests\EndToEnd;

use App\Member\Member;
use Tests\EndToEndTestCase;

class MemberSearchTest extends EndToEndTestCase
{

    public function testItHandlesFullTextSearch(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->count(2)->create(['firstname' => 'Alexander']);
        Member::factory()->defaults()->create(['firstname' => 'Heinrich']);

        sleep(1);
        $this->post(route('member.search'), ['filter' => ['search' => 'Alexander']])
            ->assertJsonCount(2, 'data');
        $this->post(route('member.search'), ['filter' => ['search' => 'Heinrich']])
            ->assertJsonCount(1, 'data');
    }
}
