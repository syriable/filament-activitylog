<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogQueryService;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Comment;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('resolves activities for a record', function () {
    $post = Post::create([
        'title' => 'Test post',
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
    ]);

    $timeline = ActivitylogTimeline::make();
    $activities = app(ActivitylogQueryService::class)->resolveForRecord($timeline, $post);

    expect($activities)
        ->toHaveCount(1);
});

it('deduplicates group activities to the highest id', function () {
    $post = Post::create([
        'title' => 'Batch post',
    ]);

    $groupKey = (string) str()->uuid();

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $latestGroupActivity = Activity::create([
        'log_name' => 'default',
        'description' => 'updated',
        'event' => 'updated',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $timeline = ActivitylogTimeline::make();
    $activities = app(ActivitylogQueryService::class)->resolveForRecord($timeline, $post);

    expect($activities)
        ->toHaveCount(1);

    expect($activities->first()->getKey())
        ->toBe($latestGroupActivity->getKey());
});

it('resolves activities from related models', function () {
    $post = Post::create([
        'title' => 'Parent post',
    ]);

    $comment = Comment::create([
        'post_id' => $post->getKey(),
        'body' => 'A comment',
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Comment::class,
        'subject_id' => $comment->getKey(),
    ]);

    $timeline = ActivitylogTimeline::make()
        ->withRelation('comments');

    $activities = app(ActivitylogQueryService::class)->resolveForRecord($timeline, $post);

    expect($activities)
        ->toHaveCount(2);
});

it('limits the number of resolved activities', function () {
    $post = Post::create([
        'title' => 'Limited post',
    ]);

    foreach (range(1, 5) as $index) {
        Activity::create([
            'log_name' => 'default',
            'description' => "event-{$index}",
            'event' => 'updated',
            'subject_type' => Post::class,
            'subject_id' => $post->getKey(),
            'created_at' => now()->subMinutes($index),
        ]);
    }

    $timeline = ActivitylogTimeline::make()
        ->activitiesLimit(2);

    $activities = app(ActivitylogQueryService::class)->resolveForRecord($timeline, $post);

    expect($activities)
        ->toHaveCount(2);
});

it('does not deduplicate grouped activities when groups are disabled', function () {
    $post = Post::create([
        'title' => 'Grouped post',
    ]);

    $groupKey = (string) str()->uuid();

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'updated',
        'event' => 'updated',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    $timeline = ActivitylogTimeline::make()
        ->activityGroups(false);

    $activities = app(ActivitylogQueryService::class)->resolveForRecord($timeline, $post);

    expect($activities)
        ->toHaveCount(2);
});
