<?php

namespace Tests\Feature\Member;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ResyncTest extends TestCase
{
    use DatabaseTransactions;

    public function testItResynchsMembers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
