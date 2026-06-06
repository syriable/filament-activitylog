<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogQueryContract;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;

class ActivitylogQueryService implements ActivitylogQueryContract
{
    public function __construct(
        protected ActivitylogGroupService $groupService,
    ) {}

    public function resolveForRecord(ActivitylogTimeline $component, Model $record): EloquentCollection
    {
        if ($component->hasCustomGetActivitiesCallback()) {
            /** @var EloquentCollection<int, ActivityModel> $activities */
            $activities = $component->evaluate($component->getGetActivitiesCallback());
        } else {
            $activities = $this->queryActivitiesForRecord($component, $record);
        }

        if ($component->supportsActivityGroups() && $component->isGroupInline()) {
            $activities = $activities->merge(
                $this->groupService->resolveInlineGroupActivities($component, $activities),
            );
        }

        if ($component->supportsActivityGroups() && $component->hasCustomGetActivitiesCallback()) {
            $activities = $this->deduplicateGroupedActivities($activities);
        }

        if ($modifyActivitiesCallback = $component->getModifyActivitiesCallback()) {
            $activities = $component->evaluate($modifyActivitiesCallback, ['activities' => $activities]);
        }

        $activities = $component->areActivitiesSortedDescending()
            ? $activities->sortByDesc('created_at')
            : $activities->sortBy('created_at');

        $activities = $activities->unique()->values();

        if ($limit = $component->getActivitiesLimit()) {
            $activities = $activities->take($limit)->values();
        }

        return $activities;
    }

    /**
     * @return EloquentCollection<int, ActivityModel>
     */
    protected function queryActivitiesForRecord(ActivitylogTimeline $component, Model $record): EloquentCollection
    {
        $activityModelClass = config('activitylog.activity_model', ActivityModel::class);

        /** @var Builder<ActivityModel> $query */
        $query = $activityModelClass::query();

        if ($modifyActivitiesQueryCallback = $component->getModifyActivitiesQueryCallback()) {
            $query = $component->evaluate(
                value: $modifyActivitiesQueryCallback,
                namedInjections: [
                    'query' => $query,
                ],
                typedInjections: [
                    $query::class => $query,
                ],
            );
        }

        $withRelations = $component
            ->getWithRelations()
            ->map(function (?Closure $modifyQueryUsing, string $relation) use ($component, $record): Relation {
                /** @var Relation<Model, Model, mixed> $relation */
                $relation = $record->{$relation}();

                if (! $modifyQueryUsing) {
                    return $relation;
                }

                return $component->evaluate(
                    $modifyQueryUsing,
                    namedInjections: [
                        'relation' => $relation,
                        'query' => $relation,
                    ],
                    typedInjections: [
                        $relation::class => $relation,
                        \Illuminate\Contracts\Database\Eloquent\Builder::class => $relation,
                    ],
                ) ?? $relation;
            })
            ->mapWithKeys(function (Relation $relation): array {
                return [$relation->getRelated()::class => $relation->pluck($relation->getRelated()->getQualifiedKeyName())];
            });

        $query->whereMorphedTo('subject', $record);

        foreach ($withRelations as $modelClass => $modelKeys) {
            $query->orWhere(function (Builder $query) use ($component, $modelClass, $modelKeys): Builder {
                if ($modifyActivitiesQueryCallback = $component->getModifyActivitiesQueryCallback()) {
                    $query = $component->evaluate(
                        value: $modifyActivitiesQueryCallback,
                        namedInjections: [
                            'query' => $query,
                        ],
                        typedInjections: [
                            $query::class => $query,
                        ],
                    );
                }

                return $query
                    ->where('subject_type', (new $modelClass())->getMorphClass())
                    ->whereIn('subject_id', $modelKeys->all());
            });
        }

        $activities = $query
            ->latest()
            ->orderByDesc('id')
            ->with(['subject', 'causer'])
            ->get()
            ->unique();

        if ($component->supportsActivityGroups()) {
            $activities = $this->deduplicateGroupedActivities($activities);
        }

        return $activities;
    }

    /**
     * @param  EloquentCollection<int, ActivityModel>  $activities
     * @return EloquentCollection<int, ActivityModel>
     */
    protected function deduplicateGroupedActivities(EloquentCollection $activities): EloquentCollection
    {
        $groupRepresentativeIds = $activities
            ->filter(fn (ActivityModel $activity): bool => ActivityBatch::getBatchUuid($activity) !== null)
            ->groupBy(fn (ActivityModel $activity): string => (string) ActivityBatch::getBatchUuid($activity))
            ->map(fn (EloquentCollection $groupActivities): int => $groupActivities->max('id'));

        return $activities
            ->filter(function (ActivityModel $activity) use ($groupRepresentativeIds): bool {
                $groupKey = ActivityBatch::getBatchUuid($activity);

                if (! $groupKey) {
                    return true;
                }

                return $activity->getKey() === $groupRepresentativeIds->get($groupKey);
            })
            ->unique(fn (ActivityModel $activity): string | int => ActivityBatch::getBatchUuid($activity) ?: $activity->id)
            ->values();
    }
}
