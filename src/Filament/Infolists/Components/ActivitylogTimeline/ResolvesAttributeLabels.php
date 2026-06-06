<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait ResolvesAttributeLabels
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, string|Closure>>
     */
    protected $attributeLabels = [];

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeLabel(string $key, string | Closure $label, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeLabels[$subjectScope][$key] = $label;
        }

        return $this;
    }

    /**
     * @param  array<string, string|Closure>  $attributeLabels
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeLabels(mixed $attributeLabels, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeLabels[$subjectScope] = [
                ...$this->attributeLabels[$subjectScope] ?? [],
                ...$attributeLabels,
            ];
        }

        return $this;
    }

    public function getAttributeLabel(ActivityModel $activity, string $key): ?string
    {
        $value = $this->resolveScopedValue($activity, $this->attributeLabels, $key);

        return $this->evaluate(
            value: $value,
            namedInjections: [
                'activity' => $activity,
                'event' => $activity->event,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );
    }
}
