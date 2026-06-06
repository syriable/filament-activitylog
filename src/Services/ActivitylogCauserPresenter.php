<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogCauserPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

class ActivitylogCauserPresenter implements ActivitylogCauserPresenterContract
{
    public function resolveName(ActivityModel $activity, ?Model $causer, ActivitylogTimeline $component): ?string
    {
        if ($component->hasCustomCauserNameCallback($causer)) {
            $name = $component->getCauserName($activity, $causer);

            return is_string($name) ? trim($name) : null;
        }

        if (! $causer) {
            return null;
        }

        if ($causer instanceof HasName) {
            return trim($causer->getFilamentName());
        }

        $attributes = $causer->getAttributes();

        if (($attributes['name'] ?? null) !== null) {
            return trim((string) $attributes['name']);
        }

        if (($attributes['first_name'] ?? null) || ($attributes['last_name'] ?? null)) {
            /** @var array<int, string|null> $nameParts */
            $nameParts = [
                $attributes['first_name'] ?? null,
                $attributes['last_name'] ?? null,
            ];

            return trim(collect($nameParts)->filter()->implode(' '));
        }

        return null;
    }

    public function resolveUrl(ActivityModel $activity, ?Model $causer, ActivitylogTimeline $component): ?string
    {
        return $component->getCauserUrl($activity, $causer);
    }
}
