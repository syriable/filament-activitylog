<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Support;

use BackedEnum;

class TimelineIconScaler
{
    public function forDisplay(string | BackedEnum | null $icon, bool $isCompact): string | BackedEnum | null
    {
        if ($icon && is_string($icon) && str($icon)->startsWith('heroicon-m-') && ! $isCompact) {
            $icon = str($icon)->replace('heroicon-m-', 'heroicon-o-');
        }

        if ($icon && is_string($icon) && str($icon)->startsWith('heroicon-o-') && $isCompact) {
            $icon = str($icon)->replace('heroicon-o-', 'heroicon-m-');
        }

        if ($icon && is_string($icon) && str($icon)->startsWith('heroicon-s-') && $isCompact) {
            $icon = str($icon)->replace('heroicon-s-', 'heroicon-m-');
        }

        return $icon;
    }
}
