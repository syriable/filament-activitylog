<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait DefinesEventDescriptions
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, string|Closure>>
     */
    protected $eventDescriptions = [];

    protected ?Closure $modifyEventDescriptionUsing = null;

    protected bool | Closure $isChangesSummaryAttributeValuesVisible = true;

    protected bool | Closure $isChangesSummaryOldAttributeValuesVisible = true;

    /**
     * @param  SubjectScopes  $subjectScopes
     */
    public function eventDescription(string $event, string | Closure $description, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->eventDescriptions[$subjectScope][$event] = $description;
        }

        return $this;
    }

    /**
     * @param  array<string, string|Closure>  $descriptions
     * @param  SubjectScopes  $subjectScopes
     */
    public function eventDescriptions(mixed $descriptions, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($subjectScopes as $subjectScope) {
            $this->eventDescriptions[$subjectScope] = [
                ...$this->eventDescriptions[$subjectScope] ?? [],
                ...$descriptions,
            ];
        }

        return $this;
    }

    public function getEventDescription(ActivityModel $activity, ?string $causerName = null, ?string $changesSummary = null): null | string | HtmlString
    {
        $value = $this->resolveScopedValue($activity, $this->eventDescriptions, $activity->event);

        return $this->evaluate(
            value: $value,
            namedInjections: [
                'activity' => $activity,
                'subject' => fn () => $activity->subject,
                'causer' => fn () => $activity->causer,
                'event' => $activity->event,
                'causerName' => $causerName,
                'changesSummary' => $changesSummary,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );
    }

    public function modifyEventDescriptionUsing(Closure $callback): static
    {
        $this->modifyEventDescriptionUsing = $callback;

        return $this;
    }

    public function hasModifyEventDescriptionCallback(): bool
    {
        return $this->modifyEventDescriptionUsing !== null;
    }

    public function modifyEventDescription(ActivityModel $activity, string $eventDescription, ?string $recordTitle, ?string $causerName, ?string $changesSummary = null): string | HtmlString
    {
        return $this->evaluate(
            value: $this->modifyEventDescriptionUsing,
            namedInjections: [
                'eventDescription' => $eventDescription,
                'activity' => $activity,
                'subject' => fn () => $activity->subject,
                'causer' => fn () => $activity->causer,
                'event' => $activity->event,
                'recordTitle' => $recordTitle,
                'causerName' => $causerName,
                'changesSummary' => $changesSummary,
            ],
            typedInjections: [
                ActivityModel::class => $activity,
                $activity::class => $activity,
            ],
        );
    }

    public function changesSummaryAttributeValues(bool | Closure $condition = true): static
    {
        $this->isChangesSummaryAttributeValuesVisible = $condition;

        return $this;
    }

    public function changesSummaryOldAttributeValues(bool | Closure $condition = true): static
    {
        $this->isChangesSummaryOldAttributeValuesVisible = $condition;

        return $this;
    }

    public function isChangesSummaryAttributeValuesVisible(string $attribute): bool
    {
        return $this->evaluate($this->isChangesSummaryAttributeValuesVisible, [
            'attribute' => $attribute,
        ]);
    }

    public function isChangesSummaryAttributeOldValuesVisible(string $attribute): bool
    {
        return $this->evaluate($this->isChangesSummaryOldAttributeValuesVisible, [
            'attribute' => $attribute,
        ]);
    }
}
