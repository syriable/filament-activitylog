<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;

trait AllowsSearching
{
    protected bool | Closure $isSearchable = false;

    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }
}
