<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Contracts;

use Illuminate\Support\HtmlString;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

interface ActivitylogDescriptionRendererContract
{
    public function format(ActivitylogEntryContext $context): string | HtmlString;
}
