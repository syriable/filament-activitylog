<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait FormatsAttributeValues
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, Closure>>
     */
    protected $attributeValues = [];

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeValue(string $key, Closure $formatUsing, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeValues[$subjectScope][$key] = $formatUsing;
        }

        return $this;
    }

    /**
     * @param  array<string, Closure>  $attributeValues
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeValues(mixed $attributeValues, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeValues[$subjectScope] = [
                ...$this->attributeValues[$subjectScope] ?? [],
                ...$attributeValues,
            ];
        }

        return $this;
    }

    public function getAttributeValueCallback(ActivityModel $activity, string $key): ?Closure
    {
        return $this->resolveScopedValue($activity, $this->attributeValues, $key);
    }

    public function formatAttributeValue(ActivityModel $activity, string $key, mixed $value, mixed $rawValue): null | string | HtmlString
    {
        return $this->evaluate(
            value: $this->getAttributeValueCallback($activity, $key),
            namedInjections: [
                'activity' => $activity,
                'event' => $activity->event,
                'value' => $value,
                'rawValue' => $rawValue,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );
    }
}
