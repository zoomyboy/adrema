<?php

use App\Contribution\Documents\BdkjHesse;
use App\Contribution\Documents\CityFrankfurtMainDocument;
use App\Contribution\Documents\CityRemscheidDocument;
use App\Contribution\Documents\CitySolingenDocument;
use App\Contribution\Documents\RdpNrwDocument;
use App\Contribution\Documents\WuppertalDocument;

dataset('contribution-validation', function () {
    return [
        [
            ['type' => 'aaa'],
            CitySolingenDocument::class,
            'type',
        ],
        [
            ['type' => ''],
            CitySolingenDocument::class,
            'type',
        ],
        [
            ['dateFrom' => ''],
            CitySolingenDocument::class,
            'dateFrom',
        ],
        [
            ['dateFrom' => '2022-01'],
            CitySolingenDocument::class,
            'dateFrom',
        ],
        [
            ['dateUntil' => ''],
            CitySolingenDocument::class,
            'dateUntil',
        ],
        [
            ['dateUntil' => '2022-01'],
            CitySolingenDocument::class,
            'dateUntil',
        ],
        [
            ['country' => -1],
            RdpNrwDocument::class,
            'country',
        ],
        [
            ['country' => 'AAAA'],
            RdpNrwDocument::class,
            'country',
        ],
        [
            ['members' => 'A'],
            RdpNrwDocument::class,
            'members',
        ],
        [
            ['members' => [99999]],
            RdpNrwDocument::class,
            'members.0',
        ],
        [
            ['members' => ['lalala']],
            RdpNrwDocument::class,
            'members.0',
        ],
        [
            ['eventName' => ''],
            CitySolingenDocument::class,
            'eventName',
        ],
        [
            ['zipLocation' => ''],
            CitySolingenDocument::class,
            'zipLocation',
        ],
        [
            ['zipLocation' => ''],
            WuppertalDocument::class,
            'zipLocation',
        ],
        [
            ['dateFrom' => ''],
            WuppertalDocument::class,
            'dateFrom',
        ],
        [
            ['dateUntil' => ''],
            WuppertalDocument::class,
            'dateUntil',
        ],
    ];
});

dataset('contribution-assertions', fn () => [
    [CitySolingenDocument::class, ["Super tolles Lager", "Max Muster", "Jane Muster", "15.06.1991"]],
    [RdpNrwDocument::class, ["Muster, Max", "Muster, Jane", "15.06.1991", "42777 SG"]],
    [CityRemscheidDocument::class, ["Max", "Muster", "Jane"]],
    [CityFrankfurtMainDocument::class, ["Max", "Muster", "Jane"]],
    [BdkjHesse::class, ["Max", "Muster", "Jane"]],
    [WuppertalDocument::class, ["Max", "Muster", "Jane", "42777 SG", "15.06.1991", "16.06.1991"]],
]);

dataset('contribution-documents', fn () => [
    CitySolingenDocument::class,
    RdpNrwDocument::class,
    CityRemscheidDocument::class,
    CityFrankfurtMainDocument::class,
    BdkjHesse::class,
    WuppertalDocument::class,
]);

