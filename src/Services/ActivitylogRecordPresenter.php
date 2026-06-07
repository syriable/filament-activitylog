<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Filament\Models\Contracts\HasName;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

class ActivitylogRecordPresenter implements ActivitylogRecordPresenterContract
{
    /**
     * @var array<string, string>
     */
    protected array $panelModelLabels = [];

    public function resolveTitle(ActivitylogEntryContext $context): ?string
    {
        if (! $context->subject) {
            return null;
        }

        return $this->resolveTitleForModel($context->activity, $context->subject, $context->component);
    }

    public function resolveTitleForModel(ActivityModel $activity, Model $model, ActivitylogTimeline $component): ?string
    {
        if ($component->hasCustomRecordTitleCallback($model)) {
            $title = $component->evaluate(
                value: $component->getRecordTitleCallbackFor($model),
                namedInjections: [
                    'activity' => $activity,
                    'model' => $model,
                ],
                typedInjections: [
                    $model::class => $model,
                    ActivityModel::class => $activity,
                    $activity::class => $activity,
                ],
            );

            return is_string($title) ? $title : null;
        }

        $recordTitleAttribute = $component->getRecordTitleAttribute($model);

        if (is_string($recordTitleAttribute)) {
            $value = $model->getAttributeValue($recordTitleAttribute);

            return is_scalar($value) ? (string) $value : null;
        }

        if ($model instanceof HasName) {
            return $model->getFilamentName();
        }

        if ($model instanceof HasLabel) {
            $label = $model->getLabel();

            return $label instanceof Htmlable ? $label->toHtml() : (string) $label;
        }

        $attributes = $model->getAttributes();

        if (($attributes['name'] ?? null) !== null) {
            return (string) $attributes['name'];
        }

        if (($attributes['title'] ?? null) !== null) {
            return (string) $attributes['title'];
        }

        if (($attributes['label'] ?? null) !== null) {
            return (string) $attributes['label'];
        }

        return null;
    }

    public function resolveSubjectModelLabel(ActivitylogEntryContext $context): ?string
    {
        $modelClass = $context->subjectClass ?? $context->modelClass;

        if (! is_string($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            return null;
        }

        if ($modelLabel = $context->component->getModelLabel($context->activity, $modelClass)) {
            return $modelLabel;
        }

        return $this->resolvePanelModelLabel($modelClass);
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function resolvePanelModelLabel(string $modelClass): string
    {
        $panel = class_exists(\Filament\Facades\Filament::class) && app()->bound('filament')
            ? \Filament\Facades\Filament::getCurrentPanel()
            : null;

        $cacheKey = ($panel?->getId() ?? 'none') . '|' . $modelClass;

        if (array_key_exists($cacheKey, $this->panelModelLabels)) {
            return $this->panelModelLabels[$cacheKey];
        }

        $modelLabel = null;

        if ($panel) {
            $modelResource = $panel->getModelResource($modelClass);

            if ($modelResource) {
                $modelLabel = $modelResource::getModelLabel();
            }
        }

        $modelLabel ??= \Filament\Support\get_model_label($modelClass);

        return $this->panelModelLabels[$cacheKey] = str($modelLabel)->lower()->toString();
    }

    public function resolveRelationshipName(ActivitylogEntryContext $context): ?string
    {
        return $this->resolveSubjectModelLabel($context);
    }

    public function resolveRecordUrl(ActivitylogEntryContext $context): ?string
    {
        if (! $context->subject) {
            return null;
        }

        return $this->resolveUrlForModel($context->activity, $context->subject, $context->component);
    }

    public function resolveUrlForModel(ActivityModel $activity, Model $model, ActivitylogTimeline $component): ?string
    {
        if ($component->hasCustomRecordUrlCallback($model)) {
            return $component->evaluate(
                value: $component->getRecordUrlCallbackFor($model),
                namedInjections: [
                    'activity' => $activity,
                    'model' => $model,
                ],
                typedInjections: [
                    $model::class => $model,
                    ActivityModel::class => $activity,
                    $activity::class => $activity,
                ],
            );
        }

        $recordUrl = null;

        if (
            class_exists(\Filament\Facades\Filament::class)
            && app()->bound('filament')
            && ($panel = \Filament\Facades\Filament::getCurrentPanel())
        ) {
            $modelResource = $panel->getModelResource($model);

            if ($modelResource && ($modelResource::hasPage('view') || $modelResource::hasPage('edit'))) {
                $parameters = [
                    'record' => $model,
                ];

                $parentResourceRegistration = $modelResource::getParentResourceRegistration();

                while ($parentResourceRegistration) {
                    $inverseRelationshipName = $parentResourceRegistration->getInverseRelationshipName();

                    $parameters[$parentResourceRegistration->getParentRouteParameterName()] = $model->{$inverseRelationshipName} ?? null;

                    $parentResourceRegistration = $parentResourceRegistration->getParentResource()::getParentResourceRegistration();
                }

                $recordUrl = $modelResource::hasPage('view') && $modelResource::canView($model)
                    ? $modelResource::getUrl('view', $parameters)
                    : ($modelResource::hasPage('edit') && $modelResource::canEdit($model) ? $modelResource::getUrl('edit', $parameters) : null);
            }
        }

        return $recordUrl;
    }
}
