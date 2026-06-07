<?php

use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogChangesSummaryAssembler;
use Syriable\Filament\Plugins\Activitylog\Support\FormSchemaLabelResolver;
use Syriable\Filament\Plugins\Activitylog\Support\SubjectModelCache;
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

it('formats each changed attribute only once for its old and new value', function () {
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

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: ActivitylogTimeline::make(),
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $attributeFormatter = Mockery::spy(ActivitylogAttributePresenterContract::class);
    $attributeFormatter->shouldReceive('format')->andReturnUsing(
        fn (ActivitylogEntryContext $context, mixed $rawValue, string $key, string $rawAttributePropertyKey = 'attributes'): ?string => is_string($rawValue) ? $rawValue : null,
    );

    $assembler = new ActivitylogChangesSummaryAssembler(
        $attributeFormatter,
        app(FormSchemaLabelResolver::class),
        app(SubjectModelCache::class),
    );

    $assembler->build($context);

    $attributeFormatter->shouldHaveReceived('format')->twice();
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
