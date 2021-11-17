<?php

namespace Tests\Feature\Initialize;

use App\Activity;
use App\Country;
use App\Gender;
use App\Nationality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;

class InitializeTest extends TestCase
{

    use RefreshDatabase;

    public function initializeProvider(callable $callback = null): void
    {
        $backend = app(FakeBackend::class)
            ->fakeLogin('123', [])
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000])
            ->fakeNationalities([['name' => 'deutsch', 'id' => 291]])
            ->fakeFees(1000, [['name' => 'Family', 'id' => 300]])
            ->fakeConfessions([['name' => 'Konf', 'id' => 301]])
            ->fakeCountries([['name' => 'Germany', 'id' => 302]])
            ->fakeGenders([['name' => 'Male', 'id' => 303]])
            ->fakeRegions([['name' => 'nrw', 'id' => 304]])
            ->fakeActivities(1000, [['name' => 'leiter', 'id' => 305]]);

        if (!$callback) {
            $backend->fakeMember([
                'vorname' => '::firstname::',
                'nachname' => '::lastname::',
                'beitragsartId' => 300,
                'geburtsDatum' => '2014-07-11 00:00:00',
                'gruppierungId' => 1000,
                'geschlechtId' => 303,
                'id' => 411,
                'eintrittsdatum' => '2020-11-17 00:00:00',
                'geschlechtId' => 303,
                'landId' => 302,
                'staatsangehoerigkeitId' => 291,
                'zeitschriftenversand' => true,
                'strasse' => '::street',
                'plz' => '12345',
                'ort' => '::location::',
                'gruppierung' => '::group::',
                'version' => 40,
            ]);
        } else {
            $callback($backend);
        }

        $backend->fakeSubactivities([
            305 => [['name' => 'wö', 'id' => 306]]
        ]);
    }

    public function testItInitializesGenders(): void
    {
        $this->withoutExceptionHandling();
        $this->initializeProvider();
        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret',
        ]);

        $this->post('/initialize');

        $this->assertDatabaseHas('regions', [
            'name' => 'nrw',
            'nami_id' => 304
        ]);
        $this->assertDatabaseHas('genders', [
            'name' => 'Male',
            'nami_id' => 303
        ]);
        $this->assertDatabaseHas('nationalities', [
            'name' => 'deutsch',
            'nami_id' => 291
        ]);
        $this->assertDatabaseHas('fees', [
            'name' => 'Family',
            'nami_id' => 300
        ]);
        $this->assertDatabaseHas('confessions', [
            'name' => 'Konf',
            'nami_id' => 301
        ]);
        $this->assertDatabaseHas('countries', [
            'name' => 'Germany',
            'nami_id' => 302
        ]);
        $this->assertDatabaseHas('activities', [
            'name' => 'Leiter',
            'nami_id' => 305
        ]);
        $this->assertDatabaseHas('groups', ['nami_id' => 1000, 'name' => '::group::']);
        $this->assertDatabaseHas('members', [
            'nami_id' => 411,
            'gender_id' => Gender::nami(303)->id,
            'country_id' => Country::nami(302)->id,
            'nationality_id' => Nationality::nami(291)->id,
            'send_newspaper' => 1,
            'address' => '::street',
            'zip' => '12345',
            'location' => '::location::',
            'version' => 40,
        ]);
        $this->assertEquals([306], Activity::where('nami_id', 305)->firstOrFail()->subactivities()->pluck('nami_id')->toArray());

        Http::assertSentCount(13);
    }

    public function testItDoesntGetMembersWithNoJoinedAtDate(): void
    {
        $this->withoutExceptionHandling();
        $this->initializeProvider(function($backend) {
            $backend->fakeMembers([$this->member(['eintrittsdatum' => null])]);
        });
        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret',
        ]);

        $this->post('/initialize');

        $this->assertDatabaseCount('members', 0);
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function pageProvider(): array
    {
        return [
            [99],
            [100],
            [101],
            [199],
            [200],
            [201],
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testItInitializesPages(int $num): void
    {
        $this->withoutExceptionHandling();
        $this->initializeProvider(function($backend) use ($num) {
            $members = collect([]);

            foreach (range(1, $num) as $i) {
                $members->push($this->member(['id' => $i]));
            }

            $backend->fakeMembers($members->toArray());
        });

        $this->post('/login', [
            'mglnr' => 123,
            'password' => 'secret',
        ]);

        $this->post('/initialize');

        $this->assertDatabaseCount('members', $num);
    }

    /**
     * @param array<string, mixed> $overwrites
     * @return array<string, mixed>
     */
    private function member(array $overwrites): array
    {
        return array_merge([
            'vorname' => '::firstname::',
            'nachname' => '::lastname::',
            'beitragsartId' => 300,
            'geburtsDatum' => '2014-07-11 00:00:00',
            'gruppierungId' => 1000,
            'geschlechtId' => 303,
            'id' => 116,
            'eintrittsdatum' => '2020-11-17 00:00:00',
            'geschlechtId' => 303,
            'landId' => 302,
            'staatsangehoerigkeitId' => 291,
            'zeitschriftenversand' => true,
            'strasse' => '::street',
            'plz' => '12345',
            'ort' => '::location::',
            'version' => 40,
            'gruppierung' => '::group::',
        ], $overwrites);
    }

}
