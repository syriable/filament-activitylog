# Filament Activitylog

Easily add beautiful timelines to your Filament apps — inside panels or stand-alone. Integrates with [Spatie Activitylog](https://github.com/spatie/laravel-activitylog).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/syriable/filament-activitylog.svg?style=flat-square)](https://packagist.org/packages/syriable/filament-activitylog)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/licenses/MIT)
[![Total Downloads](https://img.shields.io/packagist/dt/syriable/filament-activitylog.svg?style=flat-square)](https://packagist.org/packages/syriable/filament-activitylog)

**Dark mode ready** · **Multilingual support** · **Filament v4 & v5**

---

## Table of contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
  - [Logging model events](#logging-model-events)
  - [Custom events](#custom-events)
  - [Timeline](#timeline)
  - [Timeline action](#timeline-action)
- [Configuration reference](#configuration-reference)
- [Translations](#translations)
- [Troubleshooting](#troubleshooting)
- [Testing](#testing)
- [Changelog](#changelog)
- [License](#license)

## Features

- Infolist **timeline** component with many configuration options
- Searchable, compact, collapsible, and scrollable timelines
- Custom **actions** inside timeline items
- `ActivitylogTimelineAction` for pages and tables (slide-over modal)
- Activity **group** support with nested or inline display via Spatie v5 `group` properties
- Activities from **relations and related models**, with auto-linking to resources
- Global configuration of icons, colors, actions, descriptions, and more via `ActivitylogTimeline::configureUsing()`
- Beautiful design integrated with Filament
- Works **outside** the admin panel in infolists and Livewire components
- Dark mode support
- Fully translatable (English, French, Italian, Dutch included)
- Works out-of-the-box with Spatie model events

## Requirements

- PHP 8.4+
- Laravel 12 or 13
- Filament 4.10+ or 5.5+
- [Spatie Laravel Activitylog](https://spatie.be/docs/laravel-activitylog/v5/introduction) 5.x

You should already have Spatie Activitylog installed and configured before using this package. If you have existing activity data, it will work immediately.

## Installation

Install via Composer:

```bash
composer require syriable/filament-activitylog
```

The service provider registers automatically via Laravel package discovery.

### Custom theme (required)

For each Filament panel that uses the timeline, create a [custom theme](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) and add the following line to your `theme.css`:

```css
@source '../../../../vendor/syriable/filament-activitylog/resources/**/*.blade.php';
```

If you use the timeline outside a panel, include the same `@source` line in the CSS file used by the Livewire components where the timeline is rendered.

### Register the plugin

Register the plugin on each panel where you use the timeline:

```php
use Syriable\Filament\Plugins\Activitylog\Activitylog;

$panel
    ->plugin(Activitylog::make());
```

### Publish translations (optional)

```bash
php artisan vendor:publish --tag="fi-sy-activitylog-translations"
```

## Usage

Spatie Activitylog supports two main use cases:

1. Automatically **log model events** on Eloquent models.
2. Log **custom events** and associate them with Eloquent models.

### Logging model events

See the [Spatie documentation on logging model events](https://spatie.be/docs/laravel-activitylog/v5/advanced-usage/logging-model-events). Enable model event logging first, then use the timeline to display them.

### Custom events

#### Terminology

Each activity has four main properties:

1. **Subject** — the Eloquent model the event was logged on.
2. **Causer** — the model (usually a user) that caused the event, or `null`.
3. **Event** — the programmatic event name (`created`, `updated`, `deleted`, `restored`, or custom events like `published`).
4. **Description** — usually the same as the event name, but can be a custom human-readable string.

#### Logging custom events

Always include an event name. For example, logging a `published` event on a `Post`:

```php
$post = Post::find(1);
$johnDoe = User::firstWhere('email', 'johndoe@example.com');

$post->touch('published_at');

activity()
    ->performedOn($post)
    ->causedBy($johnDoe)
    ->event('published')
    ->log('published');
```

By default, the timeline displays:

> John Doe published the post.

#### Displaying the causer name

The causer name is inferred automatically:

1. If the causer implements `Filament\Models\Contracts\HasName`, `getFilamentName()` is used.
2. Otherwise, the `name` attribute is used.
3. Otherwise, `first_name` and/or `last_name` are used.
4. Otherwise, no causer name is included ("The post was published.").

Override with `causerName()` or `causerNames()`:

```php
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

ActivitylogTimeline::make()
    ->causerName(User::class, fn (User $causer) => $causer->full_name)
    ->causerNames([
        User::class => fn (User $causer) => $causer->full_name,
    ])
```

**Fallback causer name** when `causer_type` / `causer_id` are `null`:

```php
ActivitylogTimeline::make()
    ->causerName(null, 'System')
```

**Causer URL:**

```php
ActivitylogTimeline::make()
    ->causerUrl(function (User $causer) {
        return UserResource::getUrl('edit', ['record' => $causer]);
    })
```

#### Custom descriptions

If the description passed to `->log()` differs from the event name, it is stored as-is (not translatable). For translatable custom descriptions, use `eventDescription()` on the timeline component instead.

You can use inline markdown for bold or italic formatting in stored descriptions.

#### Grouping related activities

Spatie Activitylog v5 removed `LogBatch` and `Activity::forBatch()`. Use the package `ActivityBatch` helper to group related activities under a shared `group` property:

```php
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Spatie\Activitylog\Models\Activity;

ActivityBatch::withinBatch(function () use ($post) {
    activity()
        ->performedOn($post)
        ->event('published')
        ->log('published');

    $post->tweet()->create(['content' => 'Check out my new blog post']);

    activity()
        ->performedOn($post)
        ->event('tweeted')
        ->log('tweeted');
});

$groupId = ActivityBatch::getBatchUuid(Activity::query()->latest('id')->first());

ActivityBatch::scopeForBatch(Activity::query(), $groupId)->get();
```

You can also assign the property manually with `->withProperty(ActivityBatch::PROPERTY_KEY, $groupId)` when logging.

See the [Spatie v5 upgrading guide](https://github.com/spatie/laravel-activitylog/blob/main/UPGRADING.md#batch-system-removed) for details.

### Timeline

Use the timeline infolist entry in forms and infolists. In Filament v4+, infolist components can be used inside forms.

```php
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

ActivitylogTimeline::make()
    ->label('History')
```

By default, the timeline hides itself on `create` pages (no record yet). Pass `->visible(true)` to override.

When empty, a customizable empty state is shown.

#### Relation support

Include activities from related models with `withRelations()`:

```php
use Filament\Support\Icons\Heroicon;

ActivitylogTimeline::make()
    ->withRelations(['addresses'])
    ->itemIcon('*', Heroicon::OutlinedHomeModern, [Address::class])
```

Related model activities produce descriptions like "John Doe added related address **Main St. 123, NYC**."

> If a related model is no longer linked to the current record, its historical activities disappear from that record's timeline.

**Record title attribute:**

```php
ActivitylogTimeline::make()
    ->recordTitleAttribute(Post::class, 'id')
    ->recordTitleAttributes([
        Post::class => 'id',
        Address::class => 'display_summary',
    ])
    ->getRecordTitleUsing(Post::class, fn (Post $post) => (string) $post->id)
    ->getRecordTitleUsing(Address::class, fn (Address $address) => "{$address->city}, {$address->state}")
```

**Record URL:**

```php
ActivitylogTimeline::make()
    ->recordUrl(Post::class, fn (Post $post) => route('frontend.posts.show', ['post' => $post]))

// Disable linking:
ActivitylogTimeline::make()
    ->recordUrl(Post::class, fn (Post $post) => null)
```

**Limit related results:**

```php
ActivitylogTimeline::make()
    ->withRelations([
        'addresses' => function (HasMany $relation) {
            return $relation->where('type', AddressType::Shipping);
        },
    ])
```

#### Customizing timeline items

**Icons and colors:**

```php
use Filament\Support\Icons\Heroicon;
use Spatie\Activitylog\Models\Activity;

ActivitylogTimeline::make()
    ->itemIcon('created', Heroicon::OutlinedPlusCircle)
    ->itemIcon('deleted', Heroicon::OutlinedTrash)
    ->itemIcons([
        'created' => fn (Activity $activity) => Heroicon::OutlinedPlus,
        'deleted' => Heroicon::OutlinedTrash,
    ])
    ->itemIconColor('created', 'info')
    ->itemIconColor('deleted', 'danger')
    ->itemIconColors([
        'created' => 'info',
        'deleted' => 'danger',
    ])
```

Scope to specific models:

```php
ActivitylogTimeline::make()
    ->itemIcon('created', Heroicon::OutlinedPlusCircle, [Post::class])
    ->itemIconColor('created', 'success', Post::class)
```

**Badges:**

```php
use Filament\Support\Colors\Color;

ActivitylogTimeline::make()
    ->itemBadge('email_sent', fn (Activity $activity) => Status::tryFrom($activity->getProperty('status'))?->getLabel())
    ->itemBadgeColor('email_sent', fn (Activity $activity) => match (Status::tryFrom($activity->getProperty('status'))) {
        Status::Bounce => 'danger',
        Status::Delivery => 'success',
        default => 'gray',
    })
```

**Time format and timezone:**

```php
ActivitylogTimeline::make()
    ->itemDateTimeFormat('Y-m-d H:i')
    ->itemDateTimeTimezone(fn () => auth()->user()->timezone)
```

**Item actions:**

```php
use Filament\Actions;
use Filament\Forms;
use Filament\Support\Icons\Heroicon;

ActivitylogTimeline::make()
    ->itemActions('published', [
        Actions\Action::make('view_url_in_search_console')
            ->label('Open Google Search Console')
            ->icon(Heroicon::MagnifyingGlass)
            ->url(fn (Post $post) => 'https://search.google.com/...'),
        Actions\Action::make('create_tweet')
            ->label('Send Tweet')
            ->icon(Heroicon::Megaphone)
            ->form([
                Forms\Components\Textarea::make('content')
                    ->maxLength(280)
                    ->required(),
            ])
            ->action(function (Post $post, array $data) {
                // ...
            }),
    ])
```

Access the underlying activity via `$arguments['activity_id']` when needed.

**Global defaults:**

```php
use Filament\Support\Icons\Heroicon;

ActivitylogTimeline::configureUsing(function (ActivitylogTimeline $timeline) {
    return $timeline
        ->itemIcons([
            'created' => Heroicon::OutlinedPlusCircle,
            'deleted' => Heroicon::OutlinedTrash,
        ])
        ->itemIcon('created', Heroicon::OutlinedPencil, [Post::class])
        ->itemIconColors([
            'created' => 'info',
            'deleted' => 'danger',
        ])
        ->itemActions(
            event: 'published',
            actions: [/* ... */],
            subjectScopes: [Post::class],
        );
});
```

Per-timeline configuration overrides global defaults.

#### Descriptions

Override generated descriptions per event:

```php
ActivitylogTimeline::make()
    ->eventDescription('published', 'Post is now shared with the world 🌎', [Post::class])
    ->eventDescriptions(
        descriptions: [
            'created' => fn (Activity $activity) => "{$activity->causer->full_name} started a draft post",
            'published' => 'Post is now shared with the world 🌎',
        ],
        subjectScopes: [Post::class],
    )
    ->modifyEventDescriptionUsing(function (string $eventDescription, Activity $activity, string $recordTitle, ?string $causerName, ?string $changesSummary) {
        return "{$eventDescription} for {$recordTitle}";
    })
```

#### Formatting attribute values

For `updated` events, changed attributes appear in the description automatically. Strings, booleans, arrays, enums, and dates are formatted for readability.

**Custom casts** — add casted attributes to `useAttributeRawValues()` in `getActivitylogOptions()`:

```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useAttributeRawValues(['amount'])
        ->logAll();
}
```

**Format by attribute:**

```php
ActivitylogTimeline::make()
    ->attributeLabel('amount', 'purchase amount')
    ->attributeValue('amount', fn (?Money $value) => $value?->formatWithoutZeroes())
    ->attributeValues([
        'amount' => fn (?Money $value) => $value?->formatWithoutZeroes(),
    ])
```

**Format by cast class:**

```php
ActivitylogTimeline::make()
    ->attributeCast(MoneyCast::class, fn (?Money $value) => $value?->formatWithoutZeroes())
```

**Hide old attribute values:**

```php
ActivitylogTimeline::make()
    ->changesSummaryOldAttributeValues(false)
```

**Hide attribute values:**

```php
ActivitylogTimeline::make()
    ->changesSummaryAttributeValues(false)
```

**Attribute labels:**

```php
ActivitylogTimeline::make()
    ->attributeLabel('some_date', 'published at')
    ->attributeLabels([
        'some_boolean' => 'is hidden from SEO',
    ])
```

Labels are also resolved from form/infolist components on the same page when available.

**Model labels:**

```php
ActivitylogTimeline::make()
    ->modelLabel(User::class, 'Benutzer')
    ->modelLabels([
        Post::class => 'Beitrag',
    ])
```

#### Activity groups

Activities that share the same `group` property (via `ActivityBatch`) display as a nested sub-timeline by default. Display group items inline with the main timeline:

```php
ActivitylogTimeline::make()
    ->inlineGroups()
```

**Customize the group query:**

```php
ActivitylogTimeline::make()
    ->modifyGroupActivitiesQueryUsing(function (Builder $query, Activity $activity, string $groupKey) {
        return $query->whereIn('event', ['deleted', 'restored']);
    })
```

**Override the group query entirely:**

```php
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;

ActivitylogTimeline::make()
    ->getGroupActivitiesUsing(function (Activity $activity, string $groupKey) {
        return ActivityBatch::scopeForBatch(Activity::query(), $groupKey)
            ->where('id', '!=', $activity->id)
            ->get();
    })
```

#### Empty state

```php
use Filament\Support\Icons\Heroicon;

ActivitylogTimeline::make()
    ->emptyStateHeading('No history')
    ->emptyStateDescription('Nothing has happened to this model yet.')
    ->emptyStateIcon(Heroicon::OutlinedBarsArrowDown)
```

#### Compact timeline

```php
ActivitylogTimeline::make()
    ->compact()
```

Globally:

```php
ActivitylogTimeline::configureUsing(fn (ActivitylogTimeline $timeline) => $timeline->compact());
```

On compact timelines, outline heroicons are automatically converted to mini variants (and vice versa). Disable with:

```php
ActivitylogTimeline::make()
    ->convertHeroicons(false)
```

#### Searchable timelines

```php
ActivitylogTimeline::make()
    ->searchable()
```

Search runs in the browser against activity descriptions.

#### Collapsible timelines

Show only the most recent activities by default, with a toggle to reveal the rest:

```php
ActivitylogTimeline::make()
    ->collapsible()
    ->collapsedVisibleCount(5)
```

#### Maximum height

```php
ActivitylogTimeline::make()
    ->maxHeight()      // default 500px
    ->maxHeight(800)
    ->maxHeight('50vh')
```

#### Sort order

```php
ActivitylogTimeline::make()
    ->sortActivitiesDescending(false) // oldest first
```

#### Custom activities query

```php
ActivitylogTimeline::make()
    ->modifyActivitiesQueryUsing(function (Builder $query) {
        return $query->where('log_name', 'notifications');
    })
```

Override entirely:

```php
ActivitylogTimeline::make()
    ->getActivitiesUsing(function () {
        return Activity::query()->get();
    })
```

Prefer `modifyActivitiesQueryUsing()` when possible.

#### Activities limit

For models with long histories, cap how many activities are loaded into the timeline:

```php
ActivitylogTimeline::make()
    ->activitiesLimit(50)
```

The limit is applied after sorting and group deduplication. Combine with `modifyActivitiesQueryUsing()` for database-level filtering when possible.

#### Hiding the label

```php
ActivitylogTimeline::make()
    ->hiddenLabel()
```

### Timeline action

Use `ActivitylogTimelineAction` on pages and in tables to open the timeline in a slide-over:

```php
use Syriable\Filament\Plugins\Activitylog\Filament\Actions\ActivitylogTimelineAction;

class EditPost extends EditRecord
{
    protected function getHeaderActions(): array
    {
        return [
            ActivitylogTimelineAction::make(),
        ];
    }
}
```

```php
$table
    ->recordActions([
        ActivitylogTimelineAction::make(),
    ]);
```

Customize the embedded timeline:

```php
ActivitylogTimelineAction::make()
    ->modifyActivitylogTimelineUsing(function (ActivitylogTimeline $timeline) {
        return $timeline->compact()->searchable();
    })
```

## Configuration reference

| Kind | Primary | Alias |
|------|---------|-------|
| PHP namespace | `Syriable\Filament\Plugins\Activitylog` | — |
| Plugin ID | `syriable/filament-activitylog` | — |
| Translations | `fi-sy-activitylog::` | `filament-activitylog::` |
| Blade components | `<x-fi-sy-activitylog::timeline>` | `<x-filament-activitylog::timeline>` |
| CSS classes | `fi-sy-*` | — |

## Translations

Built-in languages: English, French, Italian, Dutch.

Publish and customize:

```bash
php artisan vendor:publish --tag="fi-sy-activitylog-translations"
```

## Troubleshooting

### Timeline styles are missing

Add the package `@source` line to your Filament custom theme CSS (see [Custom theme](#custom-theme-required)).

### No activities appear

Confirm Spatie Activitylog is installed, the model uses the `LogsActivity` trait, and activities exist for the record. Use `modifyActivitiesQueryUsing()` to debug the underlying query.

### Grouped activities do not collapse

Spatie v5 uses a `group` property on activities, not a `batch_uuid` column. Use `ActivityBatch::withinBatch()` or set the `group` property manually when logging.

### Performance on large histories

Use `activitiesLimit()` or `modifyActivitiesQueryUsing()` to avoid loading thousands of rows into memory.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
