<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;

trait PresentsCauser
{
    /**
     * @phpstan-var array<string|null, string|Closure>
     */
    protected $causerNames = [];

    protected null | Closure | string $causerUrl = null;

    public function causerName(?string $causerType, string | Closure $name): static
    {
        $this->causerNames[$causerType] = $name;

        return $this;
    }

    /**
     * @param  array<string|null, string|Closure>  $causerNames
     */
    public function causerNames(array $causerNames): static
    {
        $this->causerNames = [
            ...$this->causerNames,
            ...$causerNames,
        ];

        return $this;
    }

    public function hasCustomCauserNameCallback(?Model $causer): bool
    {
        return $causer
            ? isset($this->causerNames[$causer::class])
            : isset($this->causerNames[null]);
    }

    public function getCauserName(ActivityModel $activity, ?Model $causer): ?string
    {
        $value = $causer
            ? ($this->causerNames[$causer::class] ?? null)
            : ($this->causerNames[null] ?? null);

        $typedInjections = [
            ActivityModel::class => $activity,
            $activity::class => $activity,
        ];

        if ($causer) {
            $typedInjections[$causer::class] = $causer;
        }

        return $this->evaluate(
            value: $value,
            namedInjections: [
                'activity' => $activity,
                'event' => $activity->event,
                'causer' => $causer,
            ],
            typedInjections: $typedInjections,
        );
    }

    public function causerUrl(null | Closure | string $url): static
    {
        $this->causerUrl = $url;

        return $this;
    }

    public function getCauserUrl(ActivityModel $activity, ?Model $causer): ?string
    {
        $typedInjections = [
            ActivityModel::class => $activity,
            $activity::class => $activity,
        ];

        if ($causer) {
            $typedInjections[$causer::class] = $causer;
        }

        return $this->evaluate(
            value: $this->causerUrl,
            namedInjections: [
                'activity' => $activity,
                'event' => $activity->event,
                'causer' => $causer,
            ],
            typedInjections: $typedInjections,
        );
    }
}
