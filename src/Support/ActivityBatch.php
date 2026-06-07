<?php

declare(strict_types=1);

namespace Syriable\Filament\Plugins\Activitylog\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivityBatch
{
    public const PROPERTY_KEY = 'group';

    public static function getBatchUuid(ActivityModel $activity): ?string
    {
        $groupId = $activity->getProperty(self::PROPERTY_KEY);

        if (! is_string($groupId) || ! $groupId) {
            return null;
        }

        return $groupId;
    }

    /**
     * @param  Builder<ActivityModel>  $query
     * @return Builder<ActivityModel>
     */
    public static function scopeForBatch(Builder $query, string $batchUuid): Builder
    {
        return $query->where('properties->' . self::PROPERTY_KEY, $batchUuid);
    }

    /**
     * Group all activities logged during the callback under a shared `group` property.
     *
     * Replaces the removed v4 `LogBatch::withinBatch()` / `startBatch()` API.
     *
     * Activities logged in `$afterBatch` are tagged into the same group too; it
     * receives the callback result and the group id.
     *
     * Only activities created during the callbacks are tagged. Intended for single-process
     * use; for queued jobs prefer assigning `ActivityBatch::PROPERTY_KEY` explicitly.
     */
    public static function withinBatch(Closure $callback, ?Closure $afterBatch = null): mixed
    {
        $groupId = (string) Str::uuid();
        $activityModelClass = config('activitylog.activity_model', ActivityModel::class);
        $maxIdBefore = (int) $activityModelClass::query()->max('id');

        $result = $callback();

        if ($afterBatch) {
            $afterBatch($result, $groupId);
        }

        $activityModelClass::query()
            ->where('id', '>', $maxIdBefore)
            ->update(['properties->' . self::PROPERTY_KEY => $groupId]);

        return $result;
    }
}
