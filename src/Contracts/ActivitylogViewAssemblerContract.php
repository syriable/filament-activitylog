<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

interface ActivitylogViewAssemblerContract
{
    public function build(ActivitylogTimeline $component): ActivitylogTimelineViewData;
}
