<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use Closure;

trait DefinesDateTimeFormat
{
    protected string | Closure | null $itemDateTimeFormat = null;

    public function itemDateTimeFormat(string | Closure $format): static
    {
        $this->itemDateTimeFormat = $format;

        return $this;
    }

    public function getItemDateTimeFormat(): ?string
    {
        return $this->evaluate($this->itemDateTimeFormat);
    }
}
