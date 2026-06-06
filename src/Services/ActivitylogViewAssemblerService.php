<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogQueryContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogViewAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline\ActivitylogTimelineEntry;

class ActivitylogViewAssemblerService implements ActivitylogViewAssemblerContract
{
    public function __construct(
        protected ActivitylogQueryContract $activityQuery,
        protected ActivitylogSubjectLoader $subjectResolver,
        protected ActivitylogGroupService $groupService,
    ) {}

    public function build(ActivitylogTimeline $component): ActivitylogTimelineViewData
    {
        $configuration = $component->buildOptions();

        $record = $component->getRecord();

        $activities = $record instanceof Model
            ? $this->activityQuery->resolveForRecord($component, $record)
            : new EloquentCollection();

        $this->subjectResolver->preloadMissingSubjects($activities);

        $groupActivitiesMap = $this->groupService->preloadGroupActivities($component, $activities);

        $component->setPreloadedGroupActivities($groupActivitiesMap);

        $items = $activities->map(
            fn ($activity) => new ActivitylogTimelineEntry($activity, $component),
        );

        return new ActivitylogTimelineViewData(
            timelineEntries: $items,
            evaluationCallback: $component->evaluate(...),
            emptyStateHeading: $configuration->emptyStateHeading,
            emptyStateDescription: $configuration->emptyStateDescription,
            emptyStateIcon: $configuration->emptyStateIcon,
            isCompact: $configuration->isCompact,
            isSearchable: $configuration->isSearchable,
            isCollapsible: $configuration->isCollapsible,
            collapsedVisibleCount: $configuration->collapsedVisibleCount,
            maxHeight: $configuration->maxHeight,
            modelLabel: $configuration->modelLabel,
        );
    }
}
