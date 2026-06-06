<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use BackedEnum;
use Closure;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait DefinesEntryIcon
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, string|BackedEnum|Closure|null>>
     */
    protected $itemIcons = [];

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemIcon(string $event, string | BackedEnum | Closure | null $icon, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemIcons[$subjectScope][$event] = $icon;
        }

        return $this;
    }

    /**
     * @param  array<string, string|BackedEnum|Closure|null>  $icons
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemIcons(mixed $icons, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->itemIcons[$subjectScope] = [
                ...$this->itemIcons[$subjectScope] ?? [],
                ...$icons,
            ];
        }

        return $this;
    }

    public function getItemIcon(ActivityModel $activity): string | BackedEnum | null
    {
        $value = $this->resolveScopedValue($activity, $this->itemIcons, $activity->event);

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
