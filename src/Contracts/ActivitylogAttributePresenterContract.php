<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

interface ActivitylogAttributePresenterContract
{
    public function format(ActivitylogEntryContext $context, mixed $rawValue, string $key, string $rawAttributePropertyKey = 'attributes'): ?string;
}
