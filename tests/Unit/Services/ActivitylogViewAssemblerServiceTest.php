<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogViewAssemblerService;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;

it('assembles timeline view data for a record', function () {
    $post = Post::create([
        'title' => 'Timeline post',
    ]);

    Activity::create([
        'log_name' => 'default',
        'description' => 'created',
        'event' => 'created',
        'subject_type' => Post::class,
        'subject_id' => $post->getKey(),
    ]);

    $timeline = ActivitylogTimeline::make()
        ->model($post);

    $viewData = app(ActivitylogViewAssemblerService::class)->build($timeline);

    expect($viewData->getTimelineEntries())
        ->toHaveCount(1);

    expect($viewData->getTimelineEntries()->first()->getDescription())
        ->toBeInstanceOf(\Illuminate\Support\HtmlString::class);
});

it('passes collapsible configuration into timeline view data', function () {
    $post = Post::create([
        'title' => 'Collapsible post',
    ]);

    $timeline = ActivitylogTimeline::make()
        ->model($post)
        ->collapsible()
        ->collapsedVisibleCount(3);

    $viewData = app(ActivitylogViewAssemblerService::class)->build($timeline);

    expect($viewData->isCollapsible())
        ->toBeTrue();

    expect($viewData->getCollapsedVisibleCount())
        ->toBe(3);
});
