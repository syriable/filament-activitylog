<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('resolves record title from a configured attribute', function () {
    $post = Post::make([
        'title' => 'My post title',
    ]);

    $activity = new Activity([
        'event' => 'updated',
    ]);

    $timeline = ActivitylogTimeline::make()
        ->recordTitleAttribute(Post::class, 'title');

    $recordTitle = app(ActivitylogRecordPresenterContract::class)->resolveTitleForModel($activity, $post, $timeline);

    expect($recordTitle)
        ->toBe('My post title');
});

it('resolves record title using a custom callback', function () {
    $post = Post::make([
        'title' => 'Ignored title',
    ]);

    $activity = new Activity([
        'event' => 'updated',
    ]);

    $timeline = ActivitylogTimeline::make()
        ->getRecordTitleUsing(Post::class, fn () => 'Custom title');

    $recordTitle = app(ActivitylogRecordPresenterContract::class)->resolveTitleForModel($activity, $post, $timeline);

    expect($recordTitle)
        ->toBe('Custom title');
});
