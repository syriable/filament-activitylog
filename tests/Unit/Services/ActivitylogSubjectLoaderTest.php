<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogSubjectLoader;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('preloads missing subjects in a single grouped query', function () {
    $post = Post::create([
        'title' => 'Resolved post',
    ]);

    $activity = Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
    ]);

    $activities = new EloquentCollection([$activity]);

    app(ActivitylogSubjectLoader::class)->preloadMissingSubjects($activities);

    expect($activities->sole()->subject)
        ->not->toBeNull();

    expect($activities->sole()->subject->is($post))
        ->toBeTrue();
});
