<?php

use Syriable\Filament\Plugins\Activitylog\Support\TimelineIconScaler;

it('converts outline heroicons to mini in compact mode', function () {
    $heroiconConverter = app(TimelineIconScaler::class);

    expect($heroiconConverter->forDisplay('heroicon-o-bolt', true))
        ->toBe('heroicon-m-bolt');
});

it('converts mini heroicons to outline outside compact mode', function () {
    $heroiconConverter = app(TimelineIconScaler::class);

    expect($heroiconConverter->forDisplay('heroicon-m-bolt', false))
        ->toBe('heroicon-o-bolt');
});

it('converts solid heroicons to mini in compact mode', function () {
    $heroiconConverter = app(TimelineIconScaler::class);

    expect($heroiconConverter->forDisplay('heroicon-s-bolt', true))
        ->toBe('heroicon-m-bolt');
});
