<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogCauserPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\User;

it('resolves causer name from the name attribute', function () {
    $user = User::make([
        'name' => 'Jane Doe',
    ]);

    $activity = new Activity([
        'event' => 'updated',
    ]);

    $activity->setRelation('causer', $user);

    $timeline = ActivitylogTimeline::make();

    $causerName = app(ActivitylogCauserPresenterContract::class)->resolveName($activity, $user, $timeline);

    expect($causerName)
        ->toBe('Jane Doe');
});

it('resolves causer name using a custom callback', function () {
    $user = User::make([
        'name' => 'Jane Doe',
    ]);

    $activity = new Activity([
        'event' => 'updated',
    ]);

    $activity->setRelation('causer', $user);

    $timeline = ActivitylogTimeline::make()
        ->causerName(User::class, fn () => 'Custom causer');

    $causerName = app(ActivitylogCauserPresenterContract::class)->resolveName($activity, $user, $timeline);

    expect($causerName)
        ->toBe('Custom causer');
});
