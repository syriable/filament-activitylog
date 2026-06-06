<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

interface ActivitylogCauserPresenterContract
{
    public function resolveName(ActivityModel $activity, ?Model $causer, ActivitylogTimeline $component): ?string;

    public function resolveUrl(ActivityModel $activity, ?Model $causer, ActivitylogTimeline $component): ?string;
}
