<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Support;

use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Throwable;

class FormSchemaLabelResolver
{
    /**
     * @var array<string, string>
     */
    protected array $resolvedLabels = [];

    public function resolve(ActivitylogEntryContext $context, string $key): string
    {
        if ($attributeLabel = $context->component->getAttributeLabel($context->activity, $key)) {
            return $attributeLabel;
        }

        $cacheKey = spl_object_id($context->component) . ':' . $key;

        if (isset($this->resolvedLabels[$cacheKey])) {
            return $this->resolvedLabels[$cacheKey];
        }

        $attributeLabel = null;

        try {
            $livewire = $context->component->getLivewire();

            if (method_exists($livewire, 'getCachedSchemas')) {
                $cachedSchemas = $livewire->getCachedSchemas();

                foreach ($cachedSchemas ?? [] as $cachedSchema) {
                    if ($attributeLabel) {
                        continue;
                    }

                    /** @var Schema $cachedSchema */
                    $component = $cachedSchema->getComponent(function (ViewComponent $component) use ($key): bool {
                        if (! $component instanceof Field && ! $component instanceof Entry) {
                            return false;
                        }

                        return $component->getName() === $key;
                    }, withHidden: true);

                    if ($component instanceof Field || $component instanceof Entry) {
                        $label = $component->getLabel();

                        if (is_string($label)) {
                            $attributeLabel = $label;
                        } elseif ($label instanceof \Illuminate\Contracts\Support\Htmlable) {
                            $attributeLabel = $label->toHtml();
                        }
                    }
                }
            }
        } catch (Throwable) {
            $attributeLabel = null;
        }

        $attributeLabel ??= str($key)->headline()->toString();

        $this->resolvedLabels[$cacheKey] = str($attributeLabel)->lower()->toString();

        return $this->resolvedLabels[$cacheKey];
    }
}
