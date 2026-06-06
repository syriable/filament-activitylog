# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.1.0 - 2026-06-06

Initial release of `syriable/filament-activitylog` — a Filament plugin that renders Spatie Activitylog entries as a searchable, configurable timeline.

### Added

#### Public API

- `Activitylog` plugin for Filament panel registration (`make()`, `get()`, `isRegistered()`)
- `ActivitylogTimeline` infolist entry with a fluent configuration API
- `ActivitylogTimelineAction` slide-over action with `modifyActivitylogTimelineUsing()`
- `ActivitylogTimelineEntry` and `ActivitylogTimelineViewData` value objects for timeline rendering
- `ActivityBatch` helper for Spatie v5 property-based activity grouping (`withinBatch()`, `getBatchUuid()`, `scopeForBatch()`)
- Publishable views and translations under the `fi-sy-activitylog` package name

#### Timeline features

- Activity querying with custom query and resolver callbacks
- Activity group display with nested or inline group timelines using the `group` activity property
- Causer name and URL resolution
- Record title and URL resolution for related models
- Attribute label, cast, and value formatting
- Event descriptions for created, updated, deleted, restored, and custom events
- Relationship-aware descriptions with related record titles
- Changes summaries with configurable attribute visibility
- Per-item icons, icon colors, badges, badge colors, and actions
- Searchable, collapsible (with configurable visible count), and compact display modes
- `activitiesLimit()` for capping loaded timeline entries
- Configurable empty state, max height, and datetime format/timezone
- Subject preloading and grouped activity preloading for performance

#### Internal architecture

- Actions: `AssembleActivitylogTimelineAction`, `RenderActivitylogDescriptionAction`, `AssembleActivitylogChangesSummaryAction`, `RenderActivitylogAttributeValueAction`
- Services: `ActivitylogQueryService`, `ActivitylogViewAssemblerService`, `ActivitylogGroupService`, `ActivitylogSubjectLoader`, `ActivitylogRecordPresenter`, `ActivitylogCauserPresenter`, `ActivitylogAttributePresenter`, `ActivitylogChangesSummaryAssembler`, and `ActivitylogDescriptionRenderer`
- Contracts for binding custom implementations via the container
- DTOs: `ActivitylogTimelineViewData`, `ActivitylogTimelineOptions`, `ActivitylogEntryContext`
- Enums: `LoggedEventKind`, `AttributeChangeField`, `CastFormatterKind`
- Support classes: `SubjectModelCache`, `FormSchemaLabelResolver`, `TimelineIconScaler`

#### Translations

- English, French, Italian, and Dutch translation files

#### Tests

- Pest characterization tests for descriptions, queries, groups, formatters, causer/record resolution, subject preloading, and icon scaling

### Requirements

- PHP `^8.4`
- Laravel `^12.0` or `^13.0`
- Filament `^4.10` or `^5.5`
- Spatie Laravel Activitylog `^5.0`

### Installation

```bash
composer require syriable/filament-activitylog
```

Register the plugin on your Filament panel:

```php
use Syriable\Filament\Plugins\Activitylog\Activitylog;

$panel
    ->plugin(Activitylog::make());
```

Add the timeline to a resource infolist or action:

```php
use Syriable\Filament\Plugins\Activitylog\Filament\Actions\ActivitylogTimelineAction;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

ActivitylogTimeline::make()

ActivitylogTimelineAction::make()
```

Internal classes are marked `@internal` and are not part of the supported public API.
