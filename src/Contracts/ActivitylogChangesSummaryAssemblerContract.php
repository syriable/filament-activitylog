<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

interface ActivitylogChangesSummaryAssemblerContract
{
    public function build(ActivitylogEntryContext $context): ?string;
}
