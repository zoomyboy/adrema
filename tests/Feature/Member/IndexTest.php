<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use RefreshDatabase;

    public function testItGetsMembers(): void
    {
        $this->withoutExceptionHandling();
        $this->login();

        Member::factory()->defaults()->create(['firstname' => '::firstname']);
        $this->get('/member')->assertInertia('member/Index', ['firstname' => '::firstname'], 'data.data.0');
    }

}
