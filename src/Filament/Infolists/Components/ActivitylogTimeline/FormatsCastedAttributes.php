<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait FormatsCastedAttributes
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, Closure>>
     */
    protected $attributeCasts = [];

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeCast(string $cast, Closure $formatUsing, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeCasts[$subjectScope][$cast] = $formatUsing;
        }

        return $this;
    }

    /**
     * @param  array<string, Closure>  $attributeCasts
     * @param  SubjectScopes  $subjectScopes
     */
    public function attributeCasts(mixed $attributeCasts, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->attributeCasts[$subjectScope] = [
                ...$this->attributeCasts[$subjectScope] ?? [],
                ...$attributeCasts,
            ];
        }

        return $this;
    }

    public function getAttributeCastCallback(ActivityModel $activity, string $cast): ?Closure
    {
        if (str($cast)->contains(':')) {
            $cast = str($cast)->before(':')->toString();
        }

        return $this->resolveScopedValue($activity, $this->attributeCasts, $cast);
    }

    public function formatAttributeCast(ActivityModel $activity, string $cast, mixed $value, mixed $rawValue): null | string | HtmlString
    {
        return $this->evaluate(
            value: $this->getAttributeCastCallback($activity, $cast),
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
