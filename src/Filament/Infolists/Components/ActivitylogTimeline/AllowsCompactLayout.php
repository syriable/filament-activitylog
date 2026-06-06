<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;

trait AllowsCompactLayout
{
    protected bool | Closure $isCompact = false;

    protected bool | Closure $shouldConvertHeroicons = true;

    public function compact(bool | Closure $condition = true): static
    {
        $this->isCompact = $condition;

        return $this;
    }

    public function convertHeroicons(bool | Closure $condition = true): static
    {
        $this->shouldConvertHeroicons = $condition;

        return $this;
    }

    public function isCompact(): bool
    {
        return (bool) $this->evaluate($this->isCompact);
    }

    public function shouldConvertHeroicons(): bool
    {
        return (bool) $this->evaluate($this->shouldConvertHeroicons);
    }
}
