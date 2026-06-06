<?php

use Syriable\Filament\Plugins\Activitylog\Activitylog;
use Syriable\Filament\Plugins\Activitylog\Filament\Actions\ActivitylogTimelineAction;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

it('registers the plugin with the expected id', function () {
    $filamentActivitylog = Activitylog::make();

    expect($filamentActivitylog->getId())
        ->toBe('syriable/filament-activitylog');
});

it('exposes the timeline infolist component', function () {
    $timeline = ActivitylogTimeline::make();

    expect($timeline::getDefaultName())
        ->toBe('activities');
});

it('exposes the timeline action', function () {
    $timelineAction = ActivitylogTimelineAction::make();

    expect($timelineAction::getDefaultName())
        ->toBe('activities');
});

it('loads legacy translation namespace', function () {
    expect(__('filament-activitylog::translations.actions.timeline-action.label'))
        ->toBe('Activities');
});

it('loads package translation namespace', function () {
    expect(__('fi-sy-activitylog::translations.actions.timeline-action.label'))
        ->toBe('Activities');
});

it('resolves service contracts from the container', function () {
    expect(app(\Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogQueryContract::class))
        ->toBeInstanceOf(\Syriable\Filament\Plugins\Activitylog\Services\ActivitylogQueryService::class);

    expect(app(\Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogViewAssemblerContract::class))
        ->toBeInstanceOf(\Syriable\Filament\Plugins\Activitylog\Services\ActivitylogViewAssemblerService::class);

    expect(app(\Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogDescriptionRendererContract::class))
        ->toBeInstanceOf(\Syriable\Filament\Plugins\Activitylog\Services\ActivitylogDescriptionRenderer::class);
});
