<?php

namespace Tests\Feature\Pdf;

use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EfzTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCreatesAEfzPdfFile(): void
    {
        $this->withoutExceptionHandling()->login()->withNamiSettings();
        $member = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);

        $response = $this->get("/member/{$member->id}/efz");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename="efz-fuer-max-muster.pdf"');
    }
}
