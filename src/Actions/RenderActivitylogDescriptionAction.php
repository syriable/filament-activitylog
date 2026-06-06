<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Actions;

use Illuminate\Support\HtmlString;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogDescriptionRendererContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

class RenderActivitylogDescriptionAction
{
    public function __construct(
        protected ActivitylogDescriptionRendererContract $activityDescriptionFormatter,
    ) {}

    public function execute(ActivitylogEntryContext $context): string | HtmlString
    {
        return $this->activityDescriptionFormatter->format($context);
    }
}
