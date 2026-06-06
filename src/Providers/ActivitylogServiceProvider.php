<?php

namespace Syriable\Filament\Plugins\Activitylog\Providers;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogAttributePresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogCauserPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogDescriptionRendererContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogQueryContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogViewAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogAttributePresenter;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogCauserPresenter;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogChangesSummaryAssembler;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogDescriptionRenderer;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogGroupService;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogQueryService;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogRecordPresenter;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogSubjectLoader;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogViewAssemblerService;
use Syriable\Filament\Plugins\Activitylog\Support\FormSchemaLabelResolver;
use Syriable\Filament\Plugins\Activitylog\Support\SubjectModelCache;
use Syriable\Filament\Plugins\Activitylog\Support\TimelineIconScaler;

class ActivitylogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-activitylog')
            ->hasViews()
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ActivitylogGroupService::class);
        $this->app->singleton(ActivitylogSubjectLoader::class);
        $this->app->singleton(ActivitylogRecordPresenterContract::class, ActivitylogRecordPresenter::class);
        $this->app->singleton(SubjectModelCache::class);
        $this->app->singleton(FormSchemaLabelResolver::class);
        $this->app->singleton(TimelineIconScaler::class);
        $this->app->singleton(ActivitylogQueryContract::class, ActivitylogQueryService::class);
        $this->app->singleton(ActivitylogViewAssemblerContract::class, ActivitylogViewAssemblerService::class);
        $this->app->singleton(ActivitylogCauserPresenterContract::class, ActivitylogCauserPresenter::class);
        $this->app->singleton(ActivitylogAttributePresenterContract::class, ActivitylogAttributePresenter::class);
        $this->app->singleton(ActivitylogChangesSummaryAssemblerContract::class, ActivitylogChangesSummaryAssembler::class);
        $this->app->singleton(ActivitylogDescriptionRendererContract::class, ActivitylogDescriptionRenderer::class);
    }

    public function packageBooted(): void
    {
        $viewsPath = __DIR__ . '/../../resources/views';
        $langPath = __DIR__ . '/../../resources/lang';

        $this->loadViewsFrom($viewsPath, 'fi-sy-activitylog');
        $this->loadViewsFrom($viewsPath, 'filament-activitylog');
        $this->loadTranslationsFrom($langPath, 'fi-sy-activitylog');
        $this->loadTranslationsFrom($langPath, 'filament-activitylog');

        Blade::anonymousComponentPath($viewsPath . '/components', 'fi-sy-activitylog');
        Blade::anonymousComponentPath($viewsPath . '/components', 'filament-activitylog');
    }
}
