<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogGroupService;

trait SupportsActivityGroups
{
    protected bool | Closure $activityGroupsSupported = true;

    protected bool | Closure $areGroupActivitiesVisible = true;

    protected bool | Closure $isGroupInline = false;

    protected ?Closure $getGroupActivitiesCallback = null;

    protected ?Closure $modifyGroupActivitiesQueryCallback = null;

    public function supportsActivityGroups(): bool
    {
        return (bool) $this->evaluate($this->activityGroupsSupported);
    }

    public function activityGroups(bool | Closure $enabled = true): static
    {
        $this->activityGroupsSupported = $enabled;

        return $this;
    }

    public function groups(bool | Closure $visible = true): static
    {
        $this->areGroupActivitiesVisible = $visible;

        return $this;
    }

    public function inlineGroups(bool | Closure $inline = true): static
    {
        $this->isGroupInline = $inline;

        return $this;
    }

    public function getGroupActivitiesUsing(?Closure $callback): static
    {
        $this->getGroupActivitiesCallback = $callback;

        return $this;
    }

    public function modifyGroupActivitiesQueryUsing(?Closure $callback): static
    {
        $this->modifyGroupActivitiesQueryCallback = $callback;

        return $this;
    }

    public function areGroupActivitiesVisible(): bool
    {
        return $this->evaluate($this->areGroupActivitiesVisible);
    }

    public function isGroupInline(): bool
    {
        return $this->evaluate($this->isGroupInline);
    }

    public function getGetGroupActivitiesCallback(): ?Closure
    {
        return $this->getGroupActivitiesCallback;
    }

    public function getModifyGroupActivitiesQueryCallback(): ?Closure
    {
        return $this->modifyGroupActivitiesQueryCallback;
    }

    /**
     * @return EloquentCollection<int, ActivityModel>
     */
    public function getGroupActivities(ActivityModel $activity, string $groupKey): EloquentCollection
    {
        return app(ActivitylogGroupService::class)->resolveGroupActivities(
            $this,
            $activity,
            $groupKey,
            $this->getPreloadedGroupActivities(),
        );
    }
}
