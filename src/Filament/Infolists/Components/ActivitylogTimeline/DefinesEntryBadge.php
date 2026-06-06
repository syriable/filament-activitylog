<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait DefinesEntryBadge
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, string|Closure|null>>
     */
    protected $itemBadges = [];

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemBadge(string $event, string | Closure | null $badge, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemBadges[$subjectScope][$event] = $badge;
        }

        return $this;
    }

    /**
     * @param  array<string, string|Closure|null>  $badges
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemBadges(mixed $badges, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemBadges[$subjectScope] = [
                ...$this->itemBadges[$subjectScope] ?? [],
                ...$badges,
            ];
        }

        return $this;
    }

    /**
     * @return null|string|array<int|string, string>
     */
    public function getItemBadge(ActivityModel $activity): null | string | array
    {
        $value = $this->resolveScopedValue($activity, $this->itemBadges, $activity->event);

        $result = $this->evaluate(
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

        if (is_string($result) || is_array($result)) {
            return $result;
        }

        return null;
    }
}
