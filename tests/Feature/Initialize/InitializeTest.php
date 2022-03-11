<?php

namespace Tests\Feature\Initialize;

use App\Activity;
use App\Console\Commands\NamiInitializeCommand;
use App\Country;
use App\Course\Models\Course;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Setting\GeneralSettings;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Backend\FakeBackend;
use Zoomyboy\LaravelNami\Fakes\GroupFake;
use Zoomyboy\LaravelNami\Fakes\SearchFake;

class InitializeTest extends TestCase
{
    use DatabaseTransactions;

    public function initializeProvider(callable $callback = null): void
    {
        app(GroupFake::class)->fetches(null, [1000 => ['name' => 'testgroup']]);
        $backend = app(FakeBackend::class)
            ->addSearch(123, ['entries_vorname' => '::firstname::', 'entries_nachname' => '::lastname::', 'entries_gruppierungId' => 1000])
            ->fakeNationalities([['name' => 'deutsch', 'id' => 291]])
            ->fakeFees(1000, [['name' => 'Family', 'id' => 300]])
            ->fakeConfessions([['name' => 'Konf', 'id' => 301]])
            ->fakeCountries([['name' => 'Germany', 'id' => 302]])
            ->fakeGenders([['name' => 'Male', 'id' => 303]])
            ->fakeRegions([['name' => 'nrw', 'id' => 304]])
            ->fakeCourses([['name' => '1a', 'id' => 506]])
            ->fakeActivities(1000, [['name' => '€ leiter', 'id' => 305]]);

        if (!$callback) {
            $backend->fakeMember([
                'vorname' => '::firstname::',
                'nachname' => '::lastname::',
                'beitragsartId' => 300,
                'geburtsDatum' => '2014-07-11 00:00:00',
                'gruppierungId' => 1000,
                'id' => 411,
                'eintrittsdatum' => '2020-11-17 00:00:00',
                'geschlechtId' => 303,
                'landId' => 302,
                'staatsangehoerigkeitId' => 291,
                'zeitschriftenversand' => true,
                'strasse' => '::street',
                'plz' => '12346',
                'ort' => '::location::',
                'gruppierung' => '::group::',
                'version' => 40,
            ]);
        } else {
            $callback($backend);
        }

        $backend->fakeSubactivities([
            305 => [['name' => 'wö', 'id' => 306]],
        ]);
    }

    public function testItInitializesAll(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider();

        $this->post('/initialize');

        $this->assertDatabaseHas('regions', [
            'name' => 'nrw',
            'nami_id' => 304,
        ]);
        $this->assertDatabaseHas('genders', [
            'name' => 'Male',
            'nami_id' => 303,
        ]);
        $this->assertDatabaseHas('nationalities', [
            'name' => 'deutsch',
            'nami_id' => 291,
        ]);
        $this->assertDatabaseHas('fees', [
            'name' => 'Family',
            'nami_id' => 300,
        ]);
        $this->assertDatabaseHas('confessions', [
            'name' => 'Konf',
            'nami_id' => 301,
        ]);
        $this->assertDatabaseHas('countries', [
            'name' => 'Germany',
            'nami_id' => 302,
        ]);
        $this->assertDatabaseHas('activities', [
            'name' => '€ Leiter',
            'nami_id' => 305,
        ]);
        $this->assertDatabaseHas('courses', [
            'name' => '1a',
            'nami_id' => 506,
        ]);
        $this->assertDatabaseHas('groups', ['nami_id' => 1000, 'name' => '::group::']);
        $this->assertDatabaseHas('members', [
            'nami_id' => 411,
            'gender_id' => Gender::nami(303)->id,
            'country_id' => Country::nami(302)->id,
            'nationality_id' => Nationality::nami(291)->id,
            'send_newspaper' => 1,
            'address' => '::street',
            'zip' => '12346',
            'location' => '::location::',
            'version' => 40,
        ]);
        $this->assertEquals([306], Activity::where('nami_id', 305)->firstOrFail()->subactivities()->pluck('nami_id')->toArray());

        Http::assertSentCount(15);
    }

    public function testItInitializesFromCommandLine(): void
    {
        $this->withoutExceptionHandling()->loginNami();
        $this->initializeProvider();
        GeneralSettings::fake(['allowed_nami_accounts' => [123]]);

        Artisan::call(NamiInitializeCommand::class);

        $this->assertDatabaseHas('regions', [
            'name' => 'nrw',
            'nami_id' => 304,
        ]);
    }

    public function testSyncCoursesOfMember(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider(function ($backend) {
            $backend->fakeMembers([
                $this->member(['courses' => [['bausteinId' => 506, 'id' => 788, 'veranstalter' => 'KJA', 'vstgName' => 'eventname', 'vstgTag' => '2021-11-12 00:00:00']]]),
            ]);
        });
        $this->login();

        $this->post('/initialize');

        $this->assertDatabaseHas('course_members', [
            'member_id' => Member::where('firstname', '::firstname::')->firstOrFail()->id,
            'course_id' => Course::where('name', '1a')->firstOrFail()->id,
            'event_name' => 'eventname',
            'completed_at' => '2021-11-12',
            'organizer' => 'KJA',
            'nami_id' => 788,
        ]);
    }

    public function membershipDataProvider(): array
    {
        return [
            'dont_fetch_activity_from_group' => [
                [
                    'gruppierung' => '::newgroup:: 22',
                    'id' => 1077,
                    'taetigkeit' => '::newtaetigkeit:: (9001)',
                ],
                function ($db) {
                    $db->assertDatabaseCount('memberships', 0);
                },
                function ($backend) {
                    return $backend->fakeSingleMembership(116, 1077, [
                        'aktivVon' => '2021-08-22 00:00:00',
                        'aktivBis' => '',
                        'gruppierungId' => 9056,
                        'gruppierung' => '::newgroup::',
                        'id' => 1077,
                        'taetigkeit' => '::newtaetigkeit::',
                        'taetigkeitId' => 4000,
                        'untergliederungId' => 306,
                    ])
                    ->fakeActivities(9056, []);
                },
            ],
            'normal' => [
                [
                    'aktivVon' => '2021-08-22 00:00:00',
                    'aktivBis' => '',
                    'gruppierung' => '::group::',
                    'id' => 1077,
                    'taetigkeit' => '€ leiter (305)',
                    'untergliederung' => 'wö',
                ],
                function ($db) {
                    $db->assertDatabaseHas('memberships', [
                        'member_id' => Member::where('firstname', '::firstname::')->firstOrFail()->id,
                        'activity_id' => Activity::where('nami_id', 305)->firstOrFail()->id,
                        'subactivity_id' => Subactivity::where('nami_id', 306)->firstOrFail()->id,
                        'nami_id' => 1077,
                        'from' => '2021-08-22 00:00:00',
                        'group_id' => Group::where('name', '::group::')->firstOrFail()->id,
                    ]);
                },
            ],
            'fetch_subactivity_from_group' => [
                [
                    'gruppierung' => '::newgroup:: 22',
                    'id' => 1077,
                    'untergliederung' => '::newuntertaetigkeit::',
                ],
                function ($db) {
                    $db->assertDatabaseHas('subactivities', ['name' => 'wö2', 'nami_id' => 3007]);
                    $db->assertDatabaseHas('activity_subactivity', [
                        'activity_id' => Activity::where('nami_id', 305)->firstOrFail()->id,
                        'subactivity_id' => Subactivity::where('nami_id', 3007)->firstOrFail()->id,
                    ]);
                    $db->assertDatabaseHas('memberships', [
                        'activity_id' => Activity::where('nami_id', 305)->firstOrFail()->id,
                        'subactivity_id' => Subactivity::where('nami_id', 3007)->firstOrFail()->id,
                        'group_id' => Group::where('nami_id', 9056)->firstOrFail()->id,
                        'nami_id' => 1077,
                    ]);
                },
                function ($backend) {
                    return $backend->fakeSingleMembership(116, 1077, [
                        'aktivVon' => '2021-08-22 00:00:00',
                        'aktivBis' => '',
                        'gruppierungId' => 9056,
                        'gruppierung' => '::newgroup::',
                        'id' => 1077,
                        'taetigkeitId' => 305,  // default
                        'taetigkeit' => 'wö',
                        'untergliederung' => '::newuntertaetigkeit::',
                        'untergliederungId' => 3007,
                    ])
                    ->fakeActivities(9056, [['name' => '€ leiter', 'id' => 305]])
                    ->fakeSubactivities([
                        305 => [
                            ['name' => 'wö', 'id' => 306],
                            ['name' => 'wö2', 'id' => 3007],
                        ],
                    ]);
                },
            ],
            'fetch_activity_from_group' => [
                [
                    'gruppierung' => '::newgroup:: 22',
                    'id' => 1077,
                    'taetigkeit' => '::newtaetigkeit:: (9001)',
                ],
                function ($db) {
                    $db->assertDatabaseHas('activities', ['name' => '::newtaetigkeit::', 'nami_id' => 4000]);
                    $db->assertDatabaseHas('groups', ['name' => '::newgroup::', 'nami_id' => 9056]);
                    $db->assertDatabaseHas('activity_subactivity', [
                        'activity_id' => Activity::where('nami_id', 4000)->firstOrFail()->id,
                        'subactivity_id' => Subactivity::where('nami_id', 306)->firstOrFail()->id,
                    ]);
                    $db->assertDatabaseHas('memberships', [
                        'activity_id' => Activity::where('nami_id', 4000)->firstOrFail()->id,
                        'group_id' => Group::where('nami_id', 9056)->firstOrFail()->id,
                        'nami_id' => 1077,
                    ]);
                },
                function ($backend) {
                    return $backend->fakeSingleMembership(116, 1077, [
                        'aktivVon' => '2021-08-22 00:00:00',
                        'aktivBis' => '',
                        'gruppierungId' => 9056,
                        'gruppierung' => '::newgroup::',
                        'id' => 1077,
                        'taetigkeit' => '::newtaetigkeit::',
                        'taetigkeitId' => 4000,
                        'untergliederungId' => 306,
                    ])
                    ->fakeActivities(9056, [['name' => '::newtaetigkeit::', 'id' => 4000]])
                    ->fakeSubactivities([
                        4000 => [['name' => 'wö', 'id' => 306]],
                    ]);
                },
            ],
            'new_group' => [
                [
                    'gruppierung' => '::new group:: 5555',
                ],
                function ($db) {
                    $db->assertDatabaseHas('groups', ['name' => '::new group::', 'nami_id' => 5555]);
                },
                function ($backend) {
                    return $backend->fakeSingleMembership(116, 1077, [
                        'aktivVon' => '2021-08-22 00:00:00',
                        'aktivBis' => '',
                        'gruppierungId' => 5555,
                        'gruppierung' => '::new group::',
                        'id' => 1077,
                        'taetigkeitId' => 305,
                        'untergliederungId' => 306,
                    ])
                    ->fakeActivities(5555, [['name' => 'mitglied', 'id' => 310]])
                    ->fakeSubactivities([
                        310 => [['name' => 'wö', 'id' => 306]],
                    ]);
                },
            ],
            'no_subactivity' => [
                [
                    'untergliederung' => '',
                ],
                function ($db) {
                    $db->assertDatabaseHas('memberships', ['subactivity_id' => null]);
                },
            ],
            'wrong_dates' => [
                [
                    'aktivVon' => '1014-04-01 00:00:00',
                ],
                function ($db) {
                    $db->assertDatabaseHas('memberships', [
                        'from' => '1014-04-01 00:00:00',
                    ]);
                },
            ],
            'not_inactive' => [
                [
                    'aktivBis' => '2021-08-25 00:00:00',
                ],
                function ($db) {
                    $db->assertDatabaseCount('memberships', 0);
                },
            ],
        ];
    }

    /**
     * @dataProvider membershipDataProvider
     */
    public function testSyncMembershipsOfMember(array $membership, callable $dbcheck, ?callable $backendCallback = null): void
    {
        if (!$backendCallback) {
            $backendCallback = function ($backend) { return $backend; };
        }
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider(function ($backend) use ($membership, $backendCallback) {
            $backend->fakeMembers([
                $this->member([
                    'memberships' => [array_merge([
                        'aktivVon' => '2021-08-22 00:00:00',
                        'aktivBis' => '',
                        'gruppierung' => '::group::',
                        'id' => 1077,
                        'taetigkeit' => 'leiter (305)',
                        'untergliederung' => 'wö',
                    ], $membership)],
                ]),
            ]);
            $backendCallback($backend);
        });

        $this->post('/initialize');

        $dbcheck($this);
    }

    public function testItDoesntGetMembersWithNoJoinedAtDate(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider(function ($backend) {
            $backend->fakeMembers([$this->member(['eintrittsdatum' => null])]);
        });
        $this->login();

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
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider(function ($backend) use ($num) {
            $members = collect([]);

            foreach (range(1, $num) as $i) {
                $members->push($this->member(['id' => $i]));
            }

            $backend->fakeMembers($members->toArray());
        });
        $this->login();

        $this->post('/initialize');

        $this->assertDatabaseCount('members', $num);
    }

    public function testRenderErrorInConsoleWhenUsingArtisan(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $this->initializeProvider(function ($backend) {
            app(SearchFake::class)->fetchFails($page = 1, $start = 0, 'search error');
        });
        $this->login();

        $command = $this->artisan(NamiInitializeCommand::class);

        $command->assertFailed();
        $command->expectsOutput('response: {"success":false,"message":"search error"}');
        $command->expectsOutput('Search failed');
    }

    /**
     * @param array<string, mixed> $overwrites
     *
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
            'landId' => 302,
            'staatsangehoerigkeitId' => 291,
            'zeitschriftenversand' => true,
            'strasse' => '::street',
            'plz' => '12346',
            'ort' => '::location::',
            'version' => 40,
            'gruppierung' => '::group::',
        ], $overwrites);
    }
}
