<?php

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

it('formats boolean attribute values', function () {
    $subject = new class extends Model
    {
        protected $table = 'posts';

        protected $guarded = [];
    };

    $activity = new Activity([
        'event' => 'updated',
    ]);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $subject,
        subjectClass: $subject::class,
        modelClass: $subject::class,
    );

    $attributeFormatter = app(ActivitylogAttributePresenterContract::class);

    expect($attributeFormatter->format($context, true, 'is_published'))
        ->toBe(__('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.values.boolean-1'));

    expect($attributeFormatter->format($context, false, 'is_published'))
        ->toBe(__('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.values.boolean-0'));
});
