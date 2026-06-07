<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;

class ActivitylogGroupService
{
    /**
     * @return EloquentCollection<int, ActivityModel>
     */
    /**
     * @param  EloquentCollection<int, ActivityModel>  $activities
     * @return EloquentCollection<int, ActivityModel>
     */
    public function resolveInlineGroupActivities(ActivitylogTimeline $component, EloquentCollection $activities): EloquentCollection
    {
        if (! $component->supportsActivityGroups()) {
            return new EloquentCollection();
        }

        $callback = $component->getGetGroupActivitiesCallback() ?? function (EloquentCollection $activities) use ($component): EloquentCollection {
            $activityModelClass = config('activitylog.activity_model', ActivityModel::class);
            $groupKeys = $this->resolveGroupKeys($activities);

            if ($groupKeys->isEmpty()) {
                return new EloquentCollection();
            }

            $query = $activityModelClass::query()
                ->whereNotIn('id', $activities->pluck('id'))
                ->whereHas('subject')
                ->where(function (Builder $query) use ($groupKeys): void {
                    foreach ($groupKeys as $groupKey) {
                        $query->orWhere(
                            fn (Builder $groupQuery): Builder => ActivityBatch::scopeForBatch($groupQuery, $groupKey),
                        );
                    }
                });

            if ($modifyGroupActivitiesQueryCallback = $component->getModifyGroupActivitiesQueryCallback()) {
                $query = $component->evaluate(
                    value: $modifyGroupActivitiesQueryCallback,
                    namedInjections: [
                        'query' => $query,
                        'activities' => $activities,
                    ],
                    typedInjections: [
                        $query::class => $query,
                    ],
                );
            }

            return $query->get();
        };

        $groupActivities = $component->evaluate(
            value: $callback,
            namedInjections: [
                'activities' => $activities,
            ],
        );

        if (! $groupActivities instanceof EloquentCollection) {
            return new EloquentCollection();
        }

        return $groupActivities;
    }

    /**
     * @param  EloquentCollection<int, ActivityModel>  $activities
     * @return Collection<string, EloquentCollection<int, ActivityModel>>
     */
    public function preloadGroupActivities(ActivitylogTimeline $component, EloquentCollection $activities): Collection
    {
        if (! $component->supportsActivityGroups()) {
            return collect();
        }

        if (! $component->areGroupActivitiesVisible()) {
            return collect();
        }

        if ($component->isGroupInline()) {
            return collect();
        }

        $groupKeys = $this->resolveGroupKeys($activities);

        if ($groupKeys->isEmpty()) {
            return collect();
        }

        $activityModelClass = config('activitylog.activity_model', ActivityModel::class);

        $query = $activityModelClass::query()
            ->with(['subject', 'causer'])
            ->whereHas('subject')
            ->where(function (Builder $query) use ($groupKeys): void {
                foreach ($groupKeys as $groupKey) {
                    $query->orWhere(
                        fn (Builder $groupQuery): Builder => ActivityBatch::scopeForBatch($groupQuery, $groupKey),
                    );
                }
            });

        if ($modifyGroupActivitiesQueryCallback = $component->getModifyGroupActivitiesQueryCallback()) {
            $query = $component->evaluate(
                value: $modifyGroupActivitiesQueryCallback,
                namedInjections: [
                    'query' => $query,
                    'activities' => $activities,
                ],
                typedInjections: [
                    $query::class => $query,
                ],
            );
        }

        return $query
            ->get()
            ->groupBy(fn (ActivityModel $activity): ?string => ActivityBatch::getBatchUuid($activity))
            ->filter(fn (Collection $groupActivities, ?string $groupKey): bool => filled($groupKey));
    }

    /**
     * @param  Collection<string, EloquentCollection<int, ActivityModel>>|null  $preloadedGroups
     * @return EloquentCollection<int, ActivityModel>
     */
    public function resolveGroupActivities(ActivitylogTimeline $component, ActivityModel $activity, string $groupKey, ?Collection $preloadedGroups = null): EloquentCollection
    {
        if (! $component->supportsActivityGroups()) {
            return new EloquentCollection();
        }

        if (! $component->areGroupActivitiesVisible()) {
            return new EloquentCollection();
        }

        if ($preloadedGroups?->has($groupKey)) {
            $groupActivities = $preloadedGroups->get($groupKey);

            if (! $groupActivities instanceof EloquentCollection) {
                return new EloquentCollection();
            }

            if ($groupActivities->contains($activity)) {
                $groupActivities = $groupActivities->reject(
                    fn (ActivityModel $groupActivity): bool => $groupActivity->is($activity),
                );
            }

            return $groupActivities
                ->prepend($activity)
                ->sortByDesc('id')
                ->values();
        }

        $callback = $component->getGetGroupActivitiesCallback() ?? function (string $groupKey, ActivityModel $activity) use ($component): EloquentCollection {
            $activityModelClass = config('activitylog.activity_model', ActivityModel::class);

            $query = ActivityBatch::scopeForBatch($activityModelClass::query(), $groupKey)
                ->with(['subject', 'causer'])
                ->whereHas('subject')
                ->where('id', '!=', $activity->getKey());

            if ($modifyGroupActivitiesQueryCallback = $component->getModifyGroupActivitiesQueryCallback()) {
                $query = $component->evaluate(
                    value: $modifyGroupActivitiesQueryCallback,
                    namedInjections: [
                        'query' => $query,
                        'groupKey' => $groupKey,
                        'activity' => $activity,
                    ],
                    typedInjections: [
                        $query::class => $query,
                        ActivityModel::class => $activity,
                        $activity::class => $activity,
                    ],
                );
            }

            return $query->get();
        };

        $groupActivities = $component->evaluate(
            value: $callback,
            namedInjections: [
                'groupKey' => $groupKey,
                'activity' => $activity,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );

        if (! $groupActivities instanceof EloquentCollection) {
            return new EloquentCollection([$activity]);
        }

        /** @var EloquentCollection<int, ActivityModel> $groupActivities */
        if ($groupActivities->contains($activity)) {
            $groupActivities = $groupActivities->reject(
                fn (ActivityModel $groupActivity): bool => $groupActivity->is($activity),
            );
        }

        return $groupActivities
            ->prepend($activity)
            ->sortByDesc('id')
            ->values();
    }

    /**
     * @param  EloquentCollection<int, ActivityModel>  $activities
     * @return Collection<int, non-falsy-string>
     */
    protected function resolveGroupKeys(EloquentCollection $activities): Collection
    {
        return $activities
            ->map(fn (ActivityModel $activity): ?string => ActivityBatch::getBatchUuid($activity))
            ->filter()
            ->unique()
            ->values();
    }
}
