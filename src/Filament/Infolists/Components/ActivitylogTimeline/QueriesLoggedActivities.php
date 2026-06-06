<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogQueryContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;

trait QueriesLoggedActivities
{
    protected ?Closure $getActivitiesCallback = null;

    protected ?Closure $modifyActivitiesCallback = null;

    protected ?Closure $modifyActivitiesQueryCallback = null;

    protected bool | Closure $areActivitiesSortedDescending = true;

    protected int | Closure | null $activitiesLimit = null;

    /**
     * @phpstan-var list<array<string, Closure|null>|array<string|array-key, string|null|array<string|array-key, string|Closure|null>|Closure>|Closure>
     */
    protected $withRelations = [];

    /**
     * @phpstan-var array<string, string|Closure>
     */
    protected $recordTitleAttributes = [];

    /**
     * @phpstan-var array<string, ?Closure>
     */
    protected $recordTitleCallbacks = [];

    /**
     * @phpstan-var array<string, ?Closure>
     */
    protected $recordUrls = [];

    public function withRelation(string $relation, ?Closure $modifyQueryUsing = null): static
    {
        $this->withRelations[] = [$relation => $modifyQueryUsing];

        return $this;
    }

    /**
     * @param  array<string|array-key, string|null|array<string|array-key, string|Closure|null>|Closure>|Closure  $relations
     */
    public function withRelations(mixed $relations): static
    {
        $this->withRelations[] = $relations;

        return $this;
    }

    /**
     * @return Collection<string, ?Closure>
     */
    public function getWithRelations(): Collection
    {
        $withRelations = collect();

        foreach ($this->withRelations as $value) {
            $value = $this->evaluate($value);

            if (is_string($value)) {
                $withRelations[$value] = null;

                continue;
            }

            foreach ($value as $valueKey => $valueValue) {
                if (is_string($valueKey)) {
                    $withRelations[$valueKey] = $valueValue;

                    continue;
                }

                $withRelations[$valueValue] = null;
            }
        }

        return $withRelations;
    }

    public function recordTitleAttribute(string $model, string | Closure $titleAttributes): static
    {
        $this->recordTitleAttributes[$model] = $titleAttributes;

        return $this;
    }

    /**
     * @param  array<string, string|Closure>  $titleAttributes
     */
    public function recordTitleAttributes(array $titleAttributes): static
    {
        $this->recordTitleAttributes = [
            ...$this->recordTitleAttributes,
            ...$titleAttributes,
        ];

        return $this;
    }

    public function getRecordTitleAttribute(Model $model): null | string | Closure
    {
        return $this->recordTitleAttributes[$model::class] ?? null;
    }

    public function getRecordTitleUsing(string $model, ?Closure $callback): static
    {
        $this->recordTitleCallbacks[$model] = $callback;

        return $this;
    }

    /**
     * @param  array<string, ?Closure>  $callbacks
     */
    public function getRecordTitlesUsing(array $callbacks): static
    {
        $this->recordTitleCallbacks = [
            ...$this->recordTitleCallbacks,
            ...$callbacks,
        ];

        return $this;
    }

    public function hasCustomRecordTitleCallback(Model $model): bool
    {
        return isset($this->recordTitleCallbacks[$model::class]);
    }

    public function getRecordTitleCallbackFor(Model $model): ?Closure
    {
        return $this->recordTitleCallbacks[$model::class] ?? null;
    }

    public function getRecordTitle(ActivityModel $activity, Model $model): ?string
    {
        return app(ActivitylogRecordPresenterContract::class)->resolveTitleForModel($activity, $model, $this);
    }

    public function getActivitiesUsing(?Closure $callback): static
    {
        $this->getActivitiesCallback = $callback;

        return $this;
    }

    public function hasCustomGetActivitiesCallback(): bool
    {
        return isset($this->getActivitiesCallback);
    }

    public function modifyActivitiesUsing(?Closure $callback): static
    {
        $this->modifyActivitiesCallback = $callback;

        return $this;
    }

    public function modifyActivitiesQueryUsing(?Closure $callback): static
    {
        $this->modifyActivitiesQueryCallback = $callback;

        return $this;
    }

    public function getGetActivitiesCallback(): ?Closure
    {
        return $this->getActivitiesCallback;
    }

    public function getModifyActivitiesCallback(): ?Closure
    {
        return $this->modifyActivitiesCallback;
    }

    public function getModifyActivitiesQueryCallback(): ?Closure
    {
        return $this->modifyActivitiesQueryCallback;
    }

    /**
     * @return EloquentCollection<int, ActivityModel>
     */
    public function getActivities(): EloquentCollection
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return new EloquentCollection();
        }

        return app(ActivitylogQueryContract::class)->resolveForRecord($this, $record);
    }

    public function sortActivitiesDescending(bool | Closure $condition = true): static
    {
        $this->areActivitiesSortedDescending = $condition;

        return $this;
    }

    public function activitiesLimit(int | Closure | null $limit): static
    {
        $this->activitiesLimit = $limit;

        return $this;
    }

    public function areActivitiesSortedDescending(): bool
    {
        return $this->evaluate($this->areActivitiesSortedDescending);
    }

    public function getActivitiesLimit(): ?int
    {
        if ($this->activitiesLimit === null) {
            return null;
        }

        return (int) $this->evaluate($this->activitiesLimit);
    }

    public function recordUrl(string $model, ?Closure $callback): static
    {
        $this->recordUrls[$model] = $callback;

        return $this;
    }

    /**
     * @param  array<string, ?Closure>  $callbacks
     */
    public function recordUrls(array $callbacks): static
    {
        $this->recordUrls = [
            ...$this->recordUrls,
            ...$callbacks,
        ];

        return $this;
    }

    public function hasCustomRecordUrlCallback(Model $model): bool
    {
        return isset($this->recordUrls[$model::class]);
    }

    public function getRecordUrlCallbackFor(Model $model): ?Closure
    {
        return $this->recordUrls[$model::class] ?? null;
    }

    public function getRecordUrl(ActivityModel $activity, Model $model): ?string
    {
        return app(ActivitylogRecordPresenterContract::class)->resolveUrlForModel($activity, $model, $this);
    }
}
