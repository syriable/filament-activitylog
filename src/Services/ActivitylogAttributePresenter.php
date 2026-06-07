<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use BackedEnum;
use DateTimeInterface;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JsonSerializable;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Enums\CastFormatterKind;
use Syriable\Filament\Plugins\Activitylog\Support\SubjectModelCache;

class ActivitylogAttributePresenter implements ActivitylogAttributePresenterContract
{
    public function __construct(
        protected SubjectModelCache $subjectModelPrototype,
    ) {}

    public function format(ActivitylogEntryContext $context, mixed $rawValue, string $key, string $rawAttributePropertyKey = 'attributes'): ?string
    {
        $subjectPrototype = $this->subjectModelPrototype->for($context->subjectClass);

        $cast = $subjectPrototype?->getCasts()[$key] ?? null;

        $value = $rawValue;

        if ($cast) {
            if (is_array($value)) {
                $value = Json::encode($value);
            }

            /** @var Model $model */
            $model = (clone $subjectPrototype)->setRawAttributes([
                ...($context->activity->attribute_changes?->get($rawAttributePropertyKey, []) ?? []),
                $key => $value,
            ]);

            $value = $model->getAttributeValue($key);
        }

        if ($context->component->getAttributeValueCallback($context->activity, $key)) {
            $attributeValue = $context->component->formatAttributeValue($context->activity, $key, $value, $rawValue);

            if (is_string($attributeValue)) {
                $attributeValue = e($attributeValue);
            }

            return $attributeValue;
        }

        if ($cast && $context->component->getAttributeCastCallback($context->activity, $cast)) {
            $attributeValue = $context->component->formatAttributeCast($context->activity, $cast, $value, $rawValue);

            if (is_string($attributeValue)) {
                $attributeValue = e($attributeValue);
            }

            return $attributeValue;
        }

        if ($value instanceof DateTimeInterface) {
            $castType = CastFormatterKind::fromCast($cast);

            return Carbon::instance($value)->translatedFormat($castType?->format() ?? 'Y-m-d H:i:s');
        }

        if (is_array($value) || $value instanceof JsonSerializable) {
            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            if (is_array($value)) {
                return collect($value)
                    ->mapWithKeys(function (mixed $value, int | string $key): array {
                        return [
                            is_string($key) ? e($key) : $key => is_string($value) ? e($value) : $value,
                        ];
                    })
                    ->toJson();
            }
        }

        if ($value instanceof HasLabel) {
            return e($value->getLabel());
        }

        if ($value instanceof BackedEnum) {
            return $value->name;
        }

        if (is_bool($value)) {
            return $value
                ? __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.values.boolean-1')
                : __('filament-activitylog::translations.activity-timeline-item.event-descriptions.changesSummary.values.boolean-0');
        }

        return e($value);
    }
}
