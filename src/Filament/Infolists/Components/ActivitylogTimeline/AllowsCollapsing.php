<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;

trait AllowsCollapsing
{
    protected bool | Closure $isCollapsible = false;

    protected int | Closure $collapsedVisibleCount = 5;

    public function collapsible(bool | Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function collapsedVisibleCount(int | Closure $count = 5): static
    {
        $this->collapsedVisibleCount = $count;

        return $this;
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }

    public function getCollapsedVisibleCount(): int
    {
        return (int) $this->evaluate($this->collapsedVisibleCount);
    }
}
