<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;

trait DefinesDateTimeTimezone
{
    protected string | Closure | null $itemDateTimeTimezone = null;

    public function itemDateTimeTimezone(string | Closure $timezone): static
    {
        $this->itemDateTimeTimezone = $timezone;

        return $this;
    }

    public function getItemDateTimeTimezone(): ?string
    {
        return $this->evaluate($this->itemDateTimeTimezone);
    }
}
