<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use BackedEnum;
use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait DefinesEmptyState
{
    protected string | BackedEnum | Closure | null $emptyStateIcon = null;

    protected string | Htmlable | Closure | null $emptyStateHeading = null;

    protected string | Htmlable | Closure | null $emptyStateDescription = null;

    public function emptyStateIcon(string | BackedEnum | Closure | null $icon): static
    {
        $this->emptyStateIcon = $icon;

        return $this;
    }

    public function emptyStateHeading(string | Htmlable | Closure | null $heading): static
    {
        $this->emptyStateHeading = $heading;

        return $this;
    }

    public function emptyStateDescription(string | Htmlable | Closure | null $description): static
    {
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function getEmptyStateIcon(): string | BackedEnum | null
    {
        return $this->evaluate($this->emptyStateIcon);
    }

    public function getEmptyStateHeading(): ?string
    {
        return $this->evaluate($this->emptyStateHeading);
    }

    public function getEmptyStateDescription(): ?string
    {
        return $this->evaluate($this->emptyStateDescription);
    }
}
