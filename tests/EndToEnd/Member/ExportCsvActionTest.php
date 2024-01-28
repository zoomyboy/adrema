<?php

namespace Tests\EndToEnd\Member;

use App\Member\Member;
use Illuminate\Support\Facades\Storage;
use Tests\EndToEndTestCase;

class ExportCsvActionTest extends EndToEndTestCase
{
    public function testItExportsACsvFile(): void
    {
        Storage::fake('temp');

        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->postBillKind()->create(['firstname' => 'Jane', 'main_phone' => '+49 176 70343221', 'email' => 'max@muster.de']);
        Member::factory()->defaults()->emailBillKind()->create(['firstname' => 'Max']);

        sleep(1);
        $response = $this->callFilter('member-export', ['bill_kind' => 'Post']);

        $response->assertDownload('mitglieder.csv');
        $contents = Storage::disk('temp')->get('mitglieder.csv');
        $this->assertTrue(str_contains($contents, 'Jane'));
        $this->assertTrue(str_contains($contents, '+49 176 70343221'));
        $this->assertTrue(str_contains($contents, 'max@muster.de'));
        $this->assertFalse(str_contains($contents, 'Max'));
    }

    public function testItOrdersByLastname(): void
    {
        Storage::fake('temp');

        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->create(['lastname' => 'C']);
        Member::factory()->defaults()->create(['lastname' => 'A']);

        sleep(1);
        $response = $this->callFilter('member-export', []);

        $response->assertDownload('mitglieder.csv');
        $contents = Storage::disk('temp')->get('mitglieder.csv');
        $this->assertEquals(['A', 'C'], collect(explode("\n", $contents))
            ->filter(fn ($line) => $line !== '')
            ->filter(fn ($line) => !str($line)->startsWith('Nachname'))
            ->map(fn ($line) => (string) str($line)->substr(0, 1))
            ->values()
            ->toArray());
    }
}
