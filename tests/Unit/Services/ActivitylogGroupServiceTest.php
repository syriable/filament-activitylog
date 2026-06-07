<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogGroupService;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('preloads group activities grouped by group key', function () {
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

    $timeline = ActivitylogTimeline::make();
    $activities = new Illuminate\Database\Eloquent\Collection([$activityA, $activityB]);

    $groupActivitiesMap = app(ActivitylogGroupService::class)->preloadGroupActivities($timeline, $activities);

    expect($groupActivitiesMap->get($groupKey))
        ->toHaveCount(2);

    expect($groupActivitiesMap->get($groupKey)->pluck('id')->all())
        ->toBe([$activityA->getKey(), $activityB->getKey()]);
});

it('resolves group activities when a custom callback returns a non-eloquent collection', function () {
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

    $timeline = ActivitylogTimeline::make()
        ->getGroupActivitiesUsing(fn (): array => []);

    $groupActivities = app(ActivitylogGroupService::class)->resolveGroupActivities($timeline, $activity, $groupKey);

    expect($groupActivities)
        ->toHaveCount(1);

    expect($groupActivities->first()->getKey())
        ->toBe($activity->getKey());
});

it('resolves nested group activities for a timeline entry', function () {
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

    $timeline = ActivitylogTimeline::make();

    $groupActivities = app(ActivitylogGroupService::class)->resolveGroupActivities($timeline, $activityB, $groupKey);

    expect($groupActivities)
        ->toHaveCount(2);

    expect($groupActivities->pluck('id')->all())
        ->toBe([$activityB->getKey(), $activityA->getKey()]);
});
