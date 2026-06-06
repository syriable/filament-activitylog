<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

interface ActivitylogQueryContract
{
    /**
     * @return EloquentCollection<int, \Spatie\Activitylog\Models\Activity>
     */
    public function resolveForRecord(ActivitylogTimeline $component, Model $record): EloquentCollection;
}
