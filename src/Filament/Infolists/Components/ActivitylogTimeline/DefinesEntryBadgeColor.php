<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait DefinesEntryBadgeColor
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, string|FilamentColor|Closure|null>>
     */
    protected $itemBadgeColors = [];

    /**
     * @param  string|FilamentColor|Closure|null  $color
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemBadgeColor(string $event, mixed $color, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemBadgeColors[$subjectScope][$event] = $color;
        }

        return $this;
    }

    /**
     * @param  array<string, string|FilamentColor|Closure|null>  $colors
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemBadgeColors(mixed $colors, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemBadgeColors[$subjectScope] = [
                ...$this->itemBadgeColors[$subjectScope] ?? [],
                ...$colors,
            ];
        }

        return $this;
    }

    /**
     * @return null|string|array<int|string, string>
     */
    public function getItemBadgeColor(ActivityModel $activity): null | string | array
    {
        $value = $this->resolveScopedValue($activity, $this->itemBadgeColors, $activity->event);

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
