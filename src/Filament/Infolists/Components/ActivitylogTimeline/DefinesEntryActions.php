<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Filament\Actions;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Concerns\ResolvesPerSubjectConfiguration;

trait DefinesEntryActions
{
    use ResolvesPerSubjectConfiguration;

    /**
     * @phpstan-var array<string, array<string, array<int|string, Actions\Action>>>
     */
    protected $itemActions = [];

    /**
     * @param  array<array-key, Actions\Action>  $actions
     * @param  SubjectScopes  $subjectScopes
     */
    public function itemActions(string $event, mixed $actions, mixed $subjectScopes = []): static
    {
        $subjectScopes = Arr::wrap($subjectScopes);

        if (! $subjectScopes) {
            $subjectScopes = ['default'];
        }

        foreach ($actions as $action) {
            $action->schemaComponent($this);
        }

        $this->registerActions($actions);

        foreach ($subjectScopes as $subjectScope) {
            $this->itemActions[$subjectScope][$event] = [
                ...$this->itemActions[$subjectScope][$event] ?? [],
                ...$actions,
            ];
        }

        return $this;
    }

    /**
     * @return array<array-key, Actions\Action>
     */
    public function getItemActions(ActivityModel $activity): array
    {
        /** @phpstan-var array<array-key, Actions\Action> $actions */
        $actions = $this->resolveScopedValue($activity, $this->itemActions, $activity->event) ?? [];

        return array_map(
            callback: fn (Actions\Action $action): Actions\Action => (clone $action)(['activity_id' => $activity->getKey()]),
            array: $actions
        );
    }
}
