<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

interface ActivitylogRecordPresenterContract
{
    public function resolveTitle(ActivitylogEntryContext $context): ?string;

    public function resolveTitleForModel(ActivityModel $activity, Model $model, ActivitylogTimeline $component): ?string;

    public function resolveSubjectModelLabel(ActivitylogEntryContext $context): ?string;

    public function resolveRelationshipName(ActivitylogEntryContext $context): ?string;

    public function resolveRecordUrl(ActivitylogEntryContext $context): ?string;

    public function resolveUrlForModel(ActivityModel $activity, Model $model, ActivitylogTimeline $component): ?string;
}
