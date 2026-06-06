<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\DTOs;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

readonly class ActivitylogEntryContext
{
    public function __construct(
        public ActivityModel $activity,
        public ActivitylogTimeline $component,
        public ?Model $subject,
        public ?string $subjectClass,
        public ?string $modelClass,
        public bool $isBatchItem = false,
    ) {}
}
