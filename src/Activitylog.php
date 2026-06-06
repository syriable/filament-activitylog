<?php

namespace Syriable\Filament\Plugins\Activitylog;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use RuntimeException;

/**
 * Public plugin entry point for Filament panel registration.
 */
class Activitylog implements Plugin
{
    public static function make(): static
    {
        $plugin = app(static::class);

        $plugin->setUp();

        return $plugin;
    }

    public static function get(?Panel $panel = null): static
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        if (! $panel instanceof Panel) {
            throw new RuntimeException('No Filament panel is available.');
        }

        $plugin = $panel->getPlugin(app(static::class)->getId());

        if (! $plugin instanceof static) {
            throw new RuntimeException('Activitylog plugin is not registered on the panel.');
        }

        return $plugin;
    }

    public static function isRegistered(?Panel $panel = null): bool
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        if (! $panel instanceof Panel) {
            return false;
        }

        return $panel->hasPlugin(app(static::class)->getId());
    }

    public function getId(): string
    {
        return 'syriable/filament-activitylog';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function setUp(): void
    {
        //
    }
}
