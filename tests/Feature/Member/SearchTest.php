<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use DatabaseTransactions;

    public function testItHasSearchIndex(): void
    {
        $this->withoutExceptionHandling()->login();

        $member = Member::factory()->defaults()->create([
            'firstname' => '::firstname::',
            'lastname' => '::lastname::',
            'address' => 'Kölner Str 3',
            'zip' => 33333,
            'location' => 'Hilden',
        ]);

        $this->assertEquals('::firstname:: ::lastname:: Kölner Str 3, 33333 Hilden', $member->search_text);
    }

}
