<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline\ActivitylogTimelineEntry;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('returns nested group timeline data when group activities exist', function () {
    $post = Post::create([
        'title' => 'Batch post',
    ]);

    $groupKey = (string) str()->uuid();

    $activityA = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $activityB = Activity::create([
        'log_name' => 'default',
        'description' => 'updated',
        'event' => 'updated',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $activityA->setRelation('subject', $post);
    $activityB->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make();

    $timelineEntry = new ActivitylogTimelineEntry($activityB, $timeline);

    $groupViewData = $timelineEntry->getGroupViewData();

    expect($groupViewData)
        ->not->toBeNull();

    expect($groupViewData->getTimelineEntries())
        ->toHaveCount(2);
});

it('does not mutate the underlying created_at when formatting with a timezone', function () {
    $post = Post::create([
        'title' => 'Timezone post',
    ]);

    $activity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
    ]);

    $timeline = ActivitylogTimeline::make()
        ->itemDateTimeTimezone('Asia/Tokyo');

    $timelineEntry = new ActivitylogTimelineEntry($activity, $timeline);

    $timezoneBefore = $timelineEntry->getDateTime()->getTimezone()->getName();

    $timelineEntry->getDateTimeFormatted();

    expect($timelineEntry->getDateTime()->getTimezone()->getName())
        ->toBe($timezoneBefore);
});

it('keeps activity group support per instance', function () {
    $disabledTimeline = ActivitylogTimeline::make()
        ->activityGroups(false);

    $enabledTimeline = ActivitylogTimeline::make();

    expect($disabledTimeline->supportsActivityGroups())
        ->toBeFalse();

    expect($enabledTimeline->supportsActivityGroups())
        ->toBeTrue();
});

it('returns null group timeline data for inline groups', function () {
    $post = Post::create([
        'title' => 'Batch post',
    ]);

    $groupKey = (string) str()->uuid();

    $activity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make()
        ->inlineGroups();

    $timelineEntry = new ActivitylogTimelineEntry($activity, $timeline);

    expect($timelineEntry->getGroupViewData())
        ->toBeNull();
});
