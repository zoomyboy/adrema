<?php

namespace Tests\Feature\Member;

use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExportCsvActionTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function testItExportsACsvFile(): void
    {
        Storage::fake('temp');

        $this->withoutExceptionHandling()->login()->loginNami();
        Member::factory()->defaults()->postBillKind()->create(['firstname' => 'Jane', 'main_phone' => '+49 176 70343221', 'email' => 'max@muster.de']);
        Member::factory()->defaults()->emailBillKind()->create(['firstname' => 'Max']);

        $response = $this->callFilter('member-export', ['bill_kind' => 'Post']);

        $response->assertDownload('mitglieder.csv');
        $contents = Storage::disk('temp')->get('mitglieder.csv');
        $this->assertTrue(str_contains($contents, 'Jane'));
        $this->assertTrue(str_contains($contents, '+49 176 70343221'));
        $this->assertTrue(str_contains($contents, 'max@muster.de'));
        $this->assertFalse(str_contains($contents, 'Max'));
    }
}
