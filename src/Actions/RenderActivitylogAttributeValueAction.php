<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Actions;

use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;

class RenderActivitylogAttributeValueAction
{
    public function __construct(
        protected ActivitylogAttributePresenterContract $attributeFormatter,
    ) {}

    public function execute(ActivitylogEntryContext $context, mixed $rawValue, string $key, string $rawAttributePropertyKey = 'attributes'): ?string
    {
        return $this->attributeFormatter->format($context, $rawValue, $key, $rawAttributePropertyKey);
    }
}
