<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components;

use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Actions\AssembleActivitylogTimelineAction;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineOptions;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData;

class ActivitylogTimeline extends Components\Entry
{
    use ActivitylogTimeline\AllowsCollapsing;
    use ActivitylogTimeline\AllowsCompactLayout;
    use ActivitylogTimeline\AllowsSearching;
    use ActivitylogTimeline\DefinesDateTimeFormat;
    use ActivitylogTimeline\DefinesDateTimeTimezone;
    use ActivitylogTimeline\DefinesEmptyState;
    use ActivitylogTimeline\DefinesEntryActions;
    use ActivitylogTimeline\DefinesEntryBadge;
    use ActivitylogTimeline\DefinesEntryBadgeColor;
    use ActivitylogTimeline\DefinesEntryIcon;
    use ActivitylogTimeline\DefinesEntryIconColor;
    use ActivitylogTimeline\DefinesEventDescriptions;
    use ActivitylogTimeline\FormatsAttributeValues;
    use ActivitylogTimeline\FormatsCastedAttributes;
    use ActivitylogTimeline\LimitsHeight;
    use ActivitylogTimeline\PresentsCauser;
    use ActivitylogTimeline\QueriesLoggedActivities;
    use ActivitylogTimeline\ResolvesAttributeLabels;
    use ActivitylogTimeline\ResolvesModelLabel;
    use ActivitylogTimeline\SupportsActivityGroups;

    protected string $view = 'fi-sy-activitylog::infolists.components.timeline';

    /**
     * @var Collection<string, EloquentCollection<int, ActivityModel>>|null
     */
    protected ?Collection $preloadedGroupActivities = null;

    public static function getDefaultName(): ?string
    {
        return 'activities';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hidden(function (string $context): bool {
            return $context === 'create';
        });
    }

    public function resolveTimelineViewData(): ActivitylogTimelineViewData
    {
        return app(AssembleActivitylogTimelineAction::class)->execute($this);
    }

    public function buildOptions(): ActivitylogTimelineOptions
    {
        $model = $this->getModel();

        $modelLabel = $this->modelLabels[$model] ?? null;

        if (! is_string($modelLabel)) {
            $modelLabel = $model ? \Filament\Support\get_model_label($model) : null;
        }

        return new ActivitylogTimelineOptions(
            emptyStateHeading: $this->emptyStateHeading,
            emptyStateDescription: $this->emptyStateDescription,
            emptyStateIcon: $this->emptyStateIcon,
            isCompact: $this->isCompact,
            isSearchable: $this->isSearchable,
            isCollapsible: $this->isCollapsible,
            collapsedVisibleCount: $this->collapsedVisibleCount,
            maxHeight: $this->maxHeight,
            modelLabel: $modelLabel,
        );
    }

    /**
     * @param  Collection<string, EloquentCollection<int, ActivityModel>>  $groupActivities
     */
    public function setPreloadedGroupActivities(Collection $groupActivities): void
    {
        $this->preloadedGroupActivities = $groupActivities;
    }

    /**
     * @return Collection<string, EloquentCollection<int, ActivityModel>>|null
     */
    public function getPreloadedGroupActivities(): ?Collection
    {
        return $this->preloadedGroupActivities;
    }
}
