<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Enums;

enum LoggedEventKind: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';

    public static function tryFromEvent(?string $event): ?self
    {
        if (! $event) {
            return null;
        }

        return self::tryFrom($event);
    }
}
