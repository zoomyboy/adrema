<?php

namespace Tests\Feature\Pdf;

use App\Efz\EfzDocument;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\Tex\Tex;

class EfzTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCreatesAEfzPdfFile(): void
    {
        Tex::fake();
        $this->withoutExceptionHandling()->login()->withNamiSettings();
        $member = Member::factory()->defaults()->create([
            'firstname' => 'Max',
            'lastname' => 'Muster',
            'address' => 'Itt 4',
            'zip' => '12345',
            'location' => 'Solingen',
            'birthday' => '2015-02-11',
            'nami_id' => 552,
        ]);

        $response = $this->get("/member/{$member->id}/efz");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename="efz-fuer-max-muster.pdf"');
        Tex::assertCompiled(EfzDocument::class, fn ($document) => 'Max Muster' === $document->name
            && 'efz-fuer-max-muster' === $document->basename()
            && 'geb. am 11.02.2015, wohnhaft in Solingen' === $document->secondLine
            && now()->format('d.m.Y') === $document->now
            && [
                'Max Muster',
                'Itt 4',
                '12345 Solingen',
                'Mglnr.: 552',
            ] === $document->sender->values()
        );
    }

    public function testItReallyCreatesAEfzPdfFile(): void
    {
        Tex::spy();
        $this->withoutExceptionHandling()->login()->withNamiSettings();
        $member = Member::factory()->defaults()->create(['firstname' => 'Max', 'lastname' => 'Muster']);

        $response = $this->get("/member/{$member->id}/efz");

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename="efz-fuer-max-muster.pdf"');
        Tex::assertCompiled(EfzDocument::class, fn ($document) => $document->hasContent('Absender:') && $document->hasContent('Max Muster'));
    }
}
