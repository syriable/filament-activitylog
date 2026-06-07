<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('resolves a group key from activity properties', function () {
    $groupKey = (string) str()->uuid();

    $activity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'properties' => [ActivityBatch::PROPERTY_KEY => $groupKey],
    ]);

    expect(ActivityBatch::getBatchUuid($activity))
        ->toBe($groupKey);
});

it('scopes activities for a group key', function () {
    $groupKey = (string) str()->uuid();
    $post = Post::create(['title' => 'Grouped post']);

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
    ]);

    $groupedActivities = ActivityBatch::scopeForBatch(Activity::query(), $groupKey)->get();

    expect($groupedActivities)
        ->toHaveCount(1);
});

it('groups activities logged inside withinBatch', function () {
    $post = Post::create(['title' => 'Grouped post']);

    ActivityBatch::withinBatch(function () use ($post) {
        activity()
            ->performedOn($post)
            ->event('created')
            ->log('created');

        activity()
            ->performedOn($post)
            ->event('updated')
            ->log('updated');
    });

    $activities = Activity::query()->get();

    expect($activities)
        ->toHaveCount(2);

    $groupKey = ActivityBatch::getBatchUuid($activities->first());

    expect($groupKey)
        ->not->toBeNull();

    expect(ActivityBatch::getBatchUuid($activities->last()))
        ->toBe($groupKey);

    expect(ActivityBatch::scopeForBatch(Activity::query(), $groupKey)->count())
        ->toBe(2);
});

it('tags afterBatch activities into the same group and passes the result and group id', function () {
    $post = Post::create(['title' => 'Grouped post']);

    $passedResult = null;
    $passedGroupId = null;

    $result = ActivityBatch::withinBatch(
        function () use ($post): string {
            activity()
                ->performedOn($post)
                ->event('created')
                ->log('created');

            return 'pipeline-result';
        },
        function (mixed $result, string $groupId) use ($post, &$passedResult, &$passedGroupId): void {
            $passedResult = $result;
            $passedGroupId = $groupId;

            activity()
                ->performedOn($post)
                ->event('onboarding:completed')
                ->log('completed onboarding');
        },
    );

    expect($result)
        ->toBe('pipeline-result');

    expect($passedResult)
        ->toBe('pipeline-result');

    expect(Activity::query()->count())
        ->toBe(2);

    expect(ActivityBatch::scopeForBatch(Activity::query(), $passedGroupId)->count())
        ->toBe(2);
});
