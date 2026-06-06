<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;

trait ResolvesPerSubjectConfiguration
{
    protected function resolveSubjectType(ActivityModel $activity): ?string
    {
        if (! $activity->subject_type) {
            return null;
        }

        return Relation::getMorphedModel($activity->subject_type) ?? $activity->subject_type;
    }

    /**
     * @param  array<string, array<string, mixed>>  $scopedMap
     */
    /**
     * @param  array<string, array<string, mixed>>  $scopedMap
     */
    protected function resolveScopedValue(ActivityModel $activity, mixed $scopedMap, ?string $key): mixed
    {
        if ($key === null) {
            return $scopedMap['default']['*'] ?? null;
        }

        $subjectType = $this->resolveSubjectType($activity);

        if ($subjectType && Arr::has($scopedMap, "{$subjectType}.{$key}")) {
            return $scopedMap[$subjectType][$key];
        }

        if ($subjectType && Arr::has($scopedMap, "{$subjectType}.*")) {
            return $scopedMap[$subjectType]['*'];
        }

        return $scopedMap['default'][$key] ?? $scopedMap['default']['*'] ?? null;
    }
}
