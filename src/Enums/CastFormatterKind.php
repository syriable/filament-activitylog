<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Enums;

enum CastFormatterKind: string
{
    case Date = 'date';
    case ImmutableDate = 'immutable_date';
    case DateTime = 'datetime';
    case ImmutableDateTime = 'immutable_datetime';

    public function format(): string
    {
        return match ($this) {
            self::Date, self::ImmutableDate => 'Y-m-d',
            default => 'Y-m-d H:i:s',
        };
    }

    public static function fromCast(?string $cast): ?self
    {
        if (! $cast) {
            return null;
        }

        $base = str($cast)->contains(':')
            ? str($cast)->before(':')->toString()
            : $cast;

        return self::tryFrom($base);
    }
}
