<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Actions;

use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

class AssembleActivitylogChangesSummaryAction
{
    public function __construct(
        protected ActivitylogChangesSummaryAssemblerContract $changesSummaryBuilder,
    ) {}

    public function execute(ActivitylogEntryContext $context): ?string
    {
        return $this->changesSummaryBuilder->build($context);
    }
}
