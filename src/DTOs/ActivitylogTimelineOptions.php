<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\DTOs;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;

readonly class ActivitylogTimelineOptions
{
    public function __construct(
        public null | string | Htmlable | Closure $emptyStateHeading,
        public null | string | Htmlable | Closure $emptyStateDescription,
        public null | string | BackedEnum | Closure $emptyStateIcon,
        public bool | Closure $isCompact,
        public bool | Closure $isSearchable,
        public bool | Closure $isCollapsible,
        public int | Closure $collapsedVisibleCount,
        public null | string | int | Closure $maxHeight,
        public null | string | Closure $modelLabel,
    ) {}
}
