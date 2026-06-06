@php use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline\ActivitylogTimelineEntry; @endphp
@props([
    'timelineData',
    'parentTimelineEntry' => null,
    'containerKey' => null,
])

@php
    /** @var Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData $timelineData */
    
    $timelineEntries = $timelineData->getTimelineEntries();
    
    if ($parentTimelineEntry) {
        // Sort the items, so that the item that links to the parent activity timeline item goes first.
        $timelineEntries = $timelineEntries->sortBy(function (ActivitylogTimelineEntry $timelineEntry) use ($parentTimelineEntry) {
            return $timelineEntry->activity->is($parentTimelineEntry->activity) ? 0 : 1;
        });
    }
    
    $isCompact = $timelineData->isCompact();
    $isSearchable = $timelineData->isSearchable();
    $isCollapsible = $timelineData->isCollapsible();
    $collapsedVisibleCount = $timelineData->getCollapsedVisibleCount();
    $hiddenActivitiesCount = $isCollapsible
        ? max($timelineEntries->count() - $collapsedVisibleCount, 0)
        : 0;
    $maxHeight = $timelineData->getMaxHeight();
@endphp

<div
    x-data="{
        search: '',
        expanded: false,
        isCollapsible: @js($isCollapsible),
        collapsedVisibleCount: @js($collapsedVisibleCount),
    }"
    class="fi-sy-activitylog-timeline"
>
    @if($timelineData->isSearchable() && $timelineEntries->isNotEmpty())
        <x-filament::input.wrapper
            :class="\Illuminate\Support\Arr::toCssClasses([
                'mb-5' => ! $isCompact,
                'mb-4' => $isCompact,
                'fi-sy-activitylog-timeline-search-wrp',
            ])"
        >
            <x-filament::input
                type="text"
                x-model="search"
                :placeholder="__('filament-activitylog::translations.components.timeline.search.placeholder')"
                class="fi-sy-activitylog-timeline-search-input"
            />
        </x-filament::input.wrapper>
    @endif
    
    @if($timelineEntries->isNotEmpty())
        <ul
            @class([
                'flex flex-col',
                'mt-2.5' => ! $isCompact,
                'mt-1' => $isCompact,
                'overflow-y-scroll pt-1.5 pb-3' => $maxHeight,
            ])
            @style([
                "max-height: {$maxHeight}" . (is_numeric($maxHeight) ? 'px' : '') => $maxHeight,
            ])
        >
            @foreach($timelineEntries as $timelineEntry)
                @php
                    $nextItem = $timelineEntries->get($loop->index + 1);
                    $nextItemIcon = $nextItem?->getIcon($isCompact);
                
                    $icon = $timelineEntry->getIcon($isCompact);
                    
                    if ($parentTimelineEntry && $parentTimelineEntry->activity->is($timelineEntry->activity) && $icon) {
                        $icon = null;
                    }
                    
                    $iconColor = $timelineEntry->getIconColor();
                    
                    $badge = $timelineEntry->getBadge();
                    
                    $badgeColor = $timelineEntry->getBadgeColor();
                    
                    $description = $timelineEntry->getDescription();
                
                    $groupViewData = $timelineEntry->getGroupViewData();
                @endphp
                <li
                    wire:key="fi-sy-activitylog-timeline-item.{{ $containerKey }}.{{ $parentTimelineEntry?->activity->getKey() }}.{{ $timelineEntry->activity->getKey() }}"
                    @class([
                      'relative flex flex-row items-top justify-between',
                      'gap-x-2' => ! $isCompact,
                      'gap-x-2.5' => $isCompact,
                      'pb-4' => ! $loop->last && ! $isCompact,
                      'pb-2' => ! $loop->last && $isCompact,
                      '-mb-1' => $icon && $isCompact,
                    ])
                    x-data
                    x-show="(search === '' || $refs.description.textContent.toLowerCase().includes(search.toLowerCase())) && (! isCollapsible || expanded || {{ $loop->index }} < collapsedVisibleCount)"
                >
                    <div
                        @class([
                          'flex-grow-0 flex-shrink-0 flex flex-row items-top justify-center',
                          'w-[31px] pt-1.5' => ! $isCompact,
                          'w-6 pt-2.5' => $isCompact,
                        ])
                    >
                        @unless($loop->last)
                            <div
                                @class([
                                    'absolute flex justify-center',
                                    'w-6 left-[3.6px] ' => ! $isCompact,
                                    'top-5' => ! $icon && ! $isCompact,
                                    'top-8' => $icon && ! $isCompact,
                                    'bottom-0.5' => $icon && ! $nextItem->getIcon() && ! $isCompact,
                                    'bottom-0' => ! $icon && ! $nextItem->getIcon() && ! $isCompact,
                                    'bottom-2.5'  => $nextItem->getIcon() && ! $isCompact,
                                    'w-4 left-[4px] ' => $isCompact,
                                    ' top-4' => ! $icon && $isCompact,
                                    ' top-6' => $icon && $isCompact,
                                    ' bottom-0.5' => $icon && ! $nextItem->getIcon() && $isCompact,
                                    ' -bottom-1 ' => ! $icon && ! $nextItem->getIcon() && $isCompact,
                                    ' bottom-3' => $nextItem->getIcon() && $isCompact,
                                ])
                                x-show="search === '' && (! isCollapsible || expanded || {{ $loop->index }} < collapsedVisibleCount - 1)"
                            >
                                <div class="w-px bg-gray-300/70 dark:bg-gray-600/70"></div>
                            </div>
                        @endunless
                        
                        @if($parentTimelineEntry?->activity->is($timelineEntry->activity))
                            <div
                                @class([
                                    'h-px absolute bg-gray-300/70 dark:bg-gray-600/70',
                                    'h-px w-[35px] top-[9px] left-[-31px]' => ! $parentTimelineEntry->getIcon(),
                                    'h-px w-[24px] top-[9.5px] -left-5' => $parentTimelineEntry->getIcon() && ! $isCompact,
                                    'h-px w-[28px] top-[7.6px] -left-[22.5px]' => $parentTimelineEntry->getIcon() && $isCompact,
                                ])
                            ></div>
                        @endif
                        
                        @if($icon)
                            <div
                                @class([
                                    'rounded-full flex flex-row items-center justify-center',
                                    'w-7 h-7 -translate-y-2.5 bg-custom-50 text-custom-600 ring-1 ring-custom-500/90 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 ' => ! $isCompact,
                                    'w-6 h-6 -translate-y-3.5 bg-custom-50 dark:bg-custom-400/10 border border-custom-400/25' => $isCompact,
                                ])
                                @style([\Filament\Support\get_color_css_variables($iconColor ?? 'gray', shades: [50, 400, 500, 600])])
                            >
                                <x-filament::icon
                                    :icon="$icon"
                                    :class="\Illuminate\Support\Arr::toCssClasses([
                                       'text-custom-500',
                                       'w-4 h-4' => ! $isCompact,
                                       'w-4 h-4 ' => $isCompact,
                                    ])"
                                    :style="\Filament\Support\get_color_css_variables($iconColor ?? 'gray', shades: [500])"
                                />
                            </div>
                        @else
                            <div
                                @class([
                                    '-translate-y-2.5 flex flex-row items-center justify-center',
                                    'w-7 h-7' => ! $isCompact,
                                    'w-5 h-5' => $isCompact,
                                ])
                            >
                                <div
                                    @class([
                                        'bg-custom-100 text-custom-600 ring-1 ring-custom-400/80 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 rounded-full',
                                        'w-2 h-2' => ! $isCompact,
                                        'w-1.5 h-1.5' => $isCompact,
                                    ])
                                    @style([\Filament\Support\get_color_css_variables($iconColor ?? 'gray', shades: [100, 400, 600])])
                                >
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div
                        @class([
                            'flex-grow text-sm text-gray-700 dark:text-gray-300',
                            'pb-2' => $isCompact,
                        ])
                    >
                        @unless($groupViewData)
                            <p
                                x-ref="description"
                            >
                                <span
                                    class="[&_a]:after:content-['_↗'] [&_a]:underline [&_a]:underline-offset-4 [&_a]:text-primary-600 [&_a]:dark:text-primary-400"
                                >
                                    {{ $description }}
                                </span>
                                
                                @if($badge)
                                    <x-filament::badge :color="$badgeColor" class="!inline-flex">
                                        {{ $badge }}
                                    </x-filament::badge>
                                @endif
                            </p>
                        @endunless
                        
                        @if(! $groupViewData && ($actions = $timelineEntry->getActions()))
                            <x-filament::actions
                                :actions="$actions"
                                class="mt-2"
                            />
                        @endif
                        
                        @if($groupViewData)
                            <div class="-mt-1">
                                <div class="ml-4 relative">
                                    <x-fi-sy-activitylog::timeline
                                        :timeline-data="$groupViewData"
                                        :parent-timeline-entry="$timelineEntry"
                                        :container-key="$containerKey"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div
                        @class([
                            'flex-grow-0 text-sm text-gray-700 dark:text-gray-600',
                        ])
                    >
                        @unless($parentTimelineEntry?->activity->is($timelineEntry->activity))
                            <time
                                datetime="{{ ($time = $timelineEntry->getDateTime())->toDateTimeString() }}"
                                class="flex-none py-0.5 text-xs leading-5 text-gray-500 whitespace-nowrap"
                                x-data
                                x-tooltip.raw="{{ $timelineEntry->getDateTimeFormatted() }}"
                            >
                                {{ $time->shortRelativeDiffForHumans() }}
                            </time>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ul>

        @if ($hiddenActivitiesCount > 0)
            <button
                type="button"
                x-on:click="expanded = ! expanded"
                class="mt-2 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300"
            >
                <span x-show="! expanded">
                    {{ trans_choice('filament-activitylog::translations.components.timeline.collapsible.show_more', $hiddenActivitiesCount, ['count' => $hiddenActivitiesCount]) }}
                </span>

                <span x-show="expanded">
                    {{ __('filament-activitylog::translations.components.timeline.collapsible.show_less') }}
                </span>
            </button>
        @endif
    @else
        <div class="flex flex-col items-center justify-center my-10">
            @if($emptyStateIcon = $timelineData->getEmptyStateIcon())
                <div
                    class="rounded-full bg-gray-100 p-[6.5px] dark:bg-gray-500/20"
                >
                    <x-filament::icon
                        :icon="$emptyStateIcon"
                        class="h-[16px] w-[16px] text-gray-400 dark:text-gray-500"
                    />
                </div>
                
                <h4
                    class="mt-2 text-base font-medium text-black dark:text-white"
                >
                    {{ $timelineData->getEmptyStateHeading() }}
                </h4>
                
                @if($emptyStateDescription = $timelineData->getEmptyStateDescription())
                    <p
                        class="mt-0.5 text-sm text-gray-500 dark:text-gray-500"
                    >
                        {{ $emptyStateDescription }}
                    </p>
                @endif
            @endif
        </div>
    @endif
</div>