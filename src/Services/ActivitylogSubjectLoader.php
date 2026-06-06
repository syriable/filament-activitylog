<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivitylogSubjectLoader
{
    /**
     * @param  EloquentCollection<int, ActivityModel>  $activities
     */
    public function preloadMissingSubjects(EloquentCollection $activities): void
    {
        $activities
            ->filter(fn (ActivityModel $activity): bool => ! $activity->subject && $activity->subject_type && $activity->subject_id)
            ->groupBy('subject_type')
            ->each(function (Collection $group, string $subjectType): void {
                /** @var class-string<Model> $morphClass */
                $morphClass = Relation::getMorphedModel($subjectType) ?? $subjectType;
                /** @var Model $model */
                $model = new $morphClass();
                $ids = $group->pluck('subject_id');

                /** @var \Illuminate\Database\Eloquent\Builder<Model> $query */
                $query = $morphClass::query();

                if (method_exists($morphClass, 'withTrashed')) {
                    /** @var \Illuminate\Database\Eloquent\Builder<Model> $query */
                    $query = $morphClass::withTrashed();
                }

                $subjects = $query
                    ->whereIn($model->getQualifiedKeyName(), $ids)
                    ->get()
                    ->keyBy(fn (Model $subject): int | string => $subject->getKey());

                $group->each(function (ActivityModel $activity) use ($subjects): void {
                    $activity->setRelation('subject', $subjects->get($activity->subject_id));
                });
            });
    }

    public function resolveSubject(ActivityModel $activity): ?Model
    {
        if ($activity->subject) {
            return $activity->subject;
        }

        if (! $activity->subject_type || ! $activity->subject_id) {
            return null;
        }

        return $activity->subject()->withTrashed()->first();
    }

    public function resolveSubjectClass(ActivityModel $activity, ?Model $subject): ?string
    {
        if ($subject) {
            return $subject::class;
        }

        if (! $activity->subject_type) {
            return null;
        }

        return Relation::getMorphedModel($activity->subject_type) ?? $activity->subject_type;
    }
}
