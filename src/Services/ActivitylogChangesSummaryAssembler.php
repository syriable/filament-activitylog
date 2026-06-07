<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Support\FormSchemaLabelResolver;
use Syriable\Filament\Plugins\Activitylog\Support\SubjectModelCache;

class ActivitylogChangesSummaryAssembler implements ActivitylogChangesSummaryAssemblerContract
{
    public function __construct(
        protected ActivitylogAttributePresenterContract $attributeFormatter,
        protected FormSchemaLabelResolver $attributeLabelResolver,
        protected SubjectModelCache $subjectModelPrototype,
    ) {}

    public function build(ActivitylogEntryContext $context): ?string
    {
        $attributeChanges = $context->activity->attribute_changes;
        /** @var array<string, mixed> $changedAttributes */
        $changedAttributes = $attributeChanges?->get('attributes', []) ?? [];
        /** @var array<string, mixed> $changedOldAttributes */
        $changedOldAttributes = $attributeChanges?->get('old', []) ?? [];

        $attributes = collect($changedAttributes);
        $oldAttributes = collect($changedOldAttributes);

        $subjectPrototype = $this->subjectModelPrototype->for($context->subjectClass);

        $timestampColumns = $subjectPrototype ? [
            $subjectPrototype->getCreatedAtColumn(),
            $subjectPrototype->getUpdatedAtColumn(),
            method_exists($subjectPrototype, 'getDeletedAtColumn') ? $subjectPrototype->getDeletedAtColumn() : null,
        ] : [];

        $formattedAttributes = $attributes
            ->map(function (mixed $value, string $key) use ($context, $oldAttributes): array {
                return [
                    'new' => $this->attributeFormatter->format($context, $value, $key),
                    'old' => $this->attributeFormatter->format($context, $oldAttributes->get($key), $key, 'old'),
                ];
            });

        $updatedAttributes = $formattedAttributes
            ->filter(fn (array $formatted): bool => $formatted['old'] !== $formatted['new'])
            ->reject(fn (array $formatted, string $key): bool => $context->subjectClass ? in_array($key, $timestampColumns) : false);

        return $updatedAttributes
            ->map(function (array $formatted, string $key) use ($context): string {
                $attributeLabel = $this->attributeLabelResolver->resolve($context, $key);

                $isChangesSummaryAttributeValuesVisible = $context->component->isChangesSummaryAttributeValuesVisible($key);

                if (! $isChangesSummaryAttributeValuesVisible) {
                    return $attributeLabel;
                }

                $isChangesSummaryOldAttributeValuesVisible = $context->component->isChangesSummaryAttributeOldValuesVisible($key);

                $attributeValue = $formatted['new'];
                $oldAttributeValue = $formatted['old'];

                if (blank($attributeValue)) {
                    if (blank($oldAttributeValue)) {
                        return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attributeFromBlankToBlankWithOld', [
                            'attributeLabel' => $attributeLabel,
                            'oldAttributeValue' => $oldAttributeValue,
                            'newAttributeValue' => $attributeValue,
                        ]);
                    }

                    if ($isChangesSummaryOldAttributeValuesVisible) {
                        return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attributeToBlankWithOld', [
                            'attributeLabel' => $attributeLabel,
                            'oldAttributeValue' => $oldAttributeValue,
                            'newAttributeValue' => $attributeValue,
                        ]);
                    }

                    return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attributeToBlank', [
                        'attributeLabel' => $attributeLabel,
                        'newAttributeValue' => $attributeValue,
                    ]);
                }

                if ($isChangesSummaryOldAttributeValuesVisible) {
                    if (blank($oldAttributeValue)) {
                        return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attributeFromBlankWithOld', [
                            'attributeLabel' => $attributeLabel,
                            'oldAttributeValue' => $oldAttributeValue,
                            'newAttributeValue' => $attributeValue,
                        ]);
                    }

                    return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attributeWithOld', [
                        'attributeLabel' => $attributeLabel,
                        'oldAttributeValue' => $oldAttributeValue,
                        'newAttributeValue' => $attributeValue,
                    ]);
                }

                return __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.attribute', [
                    'attributeLabel' => $attributeLabel,
                    'newAttributeValue' => $attributeValue,
                ]);
            })
            ->join(', ', finalGlue: __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.finalGlue'));
    }
}
