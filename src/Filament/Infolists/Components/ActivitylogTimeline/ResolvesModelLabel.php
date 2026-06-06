<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as ActivityModel;

trait ResolvesModelLabel
{
    /**
     * @phpstan-var array<string, string|Closure|null>
     */
    protected $modelLabels = [];

    public function modelLabel(string $model, string | Closure | null $label): static
    {
        $this->modelLabels[$model] = $label;

        return $this;
    }

    /**
     * @param  array<string, string|Closure|null>  $modelLabels
     */
    public function modelLabels(mixed $modelLabels): static
    {
        $this->modelLabels = [
            ...$this->modelLabels,
            ...$modelLabels,
        ];

        return $this;
    }

    public function getModelLabel(ActivityModel $activity, Model | string $model): ?string
    {
        $modelClass = $model instanceof Model
            ? $model::class
            : $model;

        $value = $this->modelLabels[$modelClass] ?? null;

        return $this->evaluate(
            value: $value,
            namedInjections: [
                'activity' => $activity,
                'event' => $activity->event,
                'model' => $model,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );
    }
}
