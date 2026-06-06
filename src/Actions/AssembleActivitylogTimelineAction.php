<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Actions;

use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogViewAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

class AssembleActivitylogTimelineAction
{
    public function __construct(
        protected ActivitylogViewAssemblerContract $timelineBuilder,
    ) {}

    public function execute(ActivitylogTimeline $component): ActivitylogTimelineViewData
    {
        return $this->timelineBuilder->build($component);
    }
}
