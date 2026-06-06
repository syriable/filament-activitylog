<?php

namespace Syriable\Filament\Plugins\Activitylog\DTOs;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline\ActivitylogTimelineEntry;

class ActivitylogTimelineViewData
{
    /**
     * @param  Collection<int, ActivitylogTimelineEntry>  $timelineEntries
     */
    public function __construct(
        protected Collection $timelineEntries,
        protected Closure $evaluationCallback,
        protected null | string | Htmlable | Closure $emptyStateHeading = null,
        protected null | string | Htmlable | Closure $emptyStateDescription = null,
        protected null | string | BackedEnum | Closure $emptyStateIcon = null,
        protected bool | Closure $isCompact = false,
        protected bool | Closure $isSearchable = false,
        protected bool | Closure $isCollapsible = false,
        protected int | Closure $collapsedVisibleCount = 5,
        protected null | string | int | Closure $maxHeight = null,
        protected null | string | Closure $modelLabel = null,
    ) {}

    /**
     * @return Collection<int, ActivitylogTimelineEntry>
     */
    public function getTimelineEntries(): Collection
    {
        return $this->timelineEntries;
    }

    public function getEmptyStateHeading(): string
    {
        return ($this->evaluationCallback)($this->emptyStateHeading) ?? __('filament-activitylog::translations.data.timeline-configuration-data.empty-state-heading');
    }

    public function getEmptyStateDescription(): ?string
    {
        if ($this->emptyStateDescription) {
            return ($this->evaluationCallback)($this->emptyStateDescription);
        }

        if ($this->modelLabel) {
            return __('filament-activitylog::translations.data.timeline-configuration-data.empty-state-description', [
                'modelLabel' => ($this->evaluationCallback)($this->modelLabel),
            ]);
        }

        return null;
    }

    public function getEmptyStateIcon(): string | BackedEnum
    {
        return ($this->evaluationCallback)($this->emptyStateIcon) ?? 'heroicon-o-x-mark';
    }

    public function isCompact(): bool
    {
        return ($this->evaluationCallback)($this->isCompact);
    }

    public function isSearchable(): bool
    {
        return ($this->evaluationCallback)($this->isSearchable);
    }

    public function isCollapsible(): bool
    {
        return ($this->evaluationCallback)($this->isCollapsible);
    }

    public function getCollapsedVisibleCount(): int
    {
        return (int) ($this->evaluationCallback)($this->collapsedVisibleCount);
    }

    public function getMaxHeight(): string | int | null
    {
        return ($this->evaluationCallback)($this->maxHeight);
    }
}
