<?php

namespace Tests\Feature\Initialize;

use App\Activity;
use App\Initialize\InitializeActivities;
use App\Setting\NamiSettings;
use App\Subactivity;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\ActivityFake;
use Zoomyboy\LaravelNami\Fakes\GroupFake;

class InitializeActivitiesTest extends TestCase
{
    use DatabaseTransactions;

    public function testItInitializesActivities(): void
    {
        app(GroupFake::class)
            ->fetches(null, [1000 => ['name' => 'testgroup']])
            ->fetches(1000, []);
        app(ActivityFake::class)->fetches(1000, [
            ['id' => 46, 'descriptor' => 'testakt'],
        ])->fetchesSubactivity(46, [
            ['id' => 47, 'descriptor' => 'subakt'],
        ]);

        $this->withoutExceptionHandling()->login()->loginNami();

        (new InitializeActivities(app(NamiSettings::class)->login()))->handle();

        $this->assertDatabaseHas('activities', [
            'name' => 'testakt',
            'slug' => 'testakt',
            'is_filterable' => false,
            'nami_id' => 46,
            'is_member' => false,
            'is_try' => false,
            'has_efz' => false,
        ]);

        $this->assertDatabaseHas('subactivities', [
            'name' => 'subakt',
            'slug' => 'subakt',
            'is_age_group' => false,
            'is_filterable' => false,
            'nami_id' => 47,
        ]);
        $this->assertDatabaseHas('activity_subactivity', [
            'activity_id' => Activity::firstWhere('name', 'testakt')->id,
            'subactivity_id' => Subactivity::firstWhere('name', 'subakt')->id,
        ]);
    }

    public static function activityDataProvider(): Generator
    {
        yield [
            fn (ActivityFake $fake) => $fake->fetches(1000, [
                ['id' => 46, 'descriptor' => '€ Mitglied'],
            ])->fetchesSubactivity(46, [
                ['id' => 47, 'descriptor' => 'Wölfling'],
            ]),
            ['nami_id' => 46, 'is_filterable' => true, 'is_member' => true],
            ['nami_id' => 47, 'is_filterable' => true, 'is_age_group' => true],
        ];

        yield [
            fn (ActivityFake $fake) => $fake->fetches(1000, [
                ['id' => 46, 'descriptor' => 'Schnuppermitgliedschaft'],
            ])->fetchesSubactivity(46, []),
            ['nami_id' => 46, 'is_filterable' => true, 'is_member' => true, 'is_try' => true],
        ];

        yield [
            fn (ActivityFake $fake) => $fake->fetches(1000, [
                ['id' => 46, 'descriptor' => '€ LeiterIn'],
            ])->fetchesSubactivity(46, []),
            ['nami_id' => 46, 'has_efz' => true, 'is_filterable' => true],
        ];
    }

    /**
     * @param array<string, string|null> $activityCheck
     * @param array<string, string|null> $subactivityCheck
     */
    #[DataProvider('activityDataProvider')]
    public function testItInitsOtherFields(callable $activityFake, array $activityCheck, ?array $subactivityCheck = null): void
    {
        app(GroupFake::class)
            ->fetches(null, [1000 => ['name' => 'testgroup']])
            ->fetches(1000, []);

        $activityFake(app(ActivityFake::class));

        $this->withoutExceptionHandling()->login()->loginNami();

        (new InitializeActivities(app(NamiSettings::class)->login()))->handle();

        $this->assertDatabaseHas('activities', $activityCheck);

        if ($subactivityCheck) {
            $this->assertDatabaseHas('subactivities', $subactivityCheck);
        }
    }
}
