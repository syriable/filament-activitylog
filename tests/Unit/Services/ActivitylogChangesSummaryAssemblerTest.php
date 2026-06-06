<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('builds a changes summary for updated attributes', function () {
    $post = Post::make([
        'title' => 'New title',
    ]);

    $activity = new Activity([
        'event' => 'updated',
        'attribute_changes' => [
            'attributes' => ['title' => 'New title'],
            'old' => ['title' => 'Old title'],
        ],
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $changesSummary = app(ActivitylogChangesSummaryAssemblerContract::class)->build($context);

    expect($changesSummary)
        ->toContain('title')
        ->toContain('New title');
});

it('hides attribute values when configured', function () {
    $post = Post::make([
        'title' => 'New title',
    ]);

    $activity = new Activity([
        'event' => 'updated',
        'attribute_changes' => [
            'attributes' => ['title' => 'New title'],
            'old' => ['title' => 'Old title'],
        ],
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make()
        ->changesSummaryAttributeValues(false);

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $changesSummary = app(ActivitylogChangesSummaryAssemblerContract::class)->build($context);

    expect($changesSummary)
        ->toContain('title')
        ->not->toContain('New title');
});
