<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $getExtraAttributeBag() }}>
        <x-fi-sy-activitylog::timeline
            :timeline-data="$resolveTimelineViewData()"
            :container-key="$getKey()"
        />
    </div>
</x-dynamic-component>