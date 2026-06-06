<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Syriable\Filament\Plugins\Activitylog\Actions\AssembleActivitylogChangesSummaryAction;
use Syriable\Filament\Plugins\Activitylog\Actions\RenderActivitylogAttributeValueAction;
use Syriable\Filament\Plugins\Activitylog\Actions\RenderActivitylogDescriptionAction;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogCauserPresenterContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogTimelineViewData;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Services\ActivitylogSubjectLoader;
use Syriable\Filament\Plugins\Activitylog\Support\ActivityBatch;
use Syriable\Filament\Plugins\Activitylog\Support\FormSchemaLabelResolver;
use Syriable\Filament\Plugins\Activitylog\Support\TimelineIconScaler;
use Throwable;

class ActivitylogTimelineEntry
{
    public readonly ?Model $subject;

    public readonly ?string $subjectClass;

    public readonly ?string $model;

    protected ActivitylogEntryContext $context;

    public function __construct(
        public readonly ActivityModel $activity,
        public readonly ActivitylogTimeline $component,
        public readonly bool $isGroupEntry = false,
    ) {
        $subjectResolver = app(ActivitylogSubjectLoader::class);

        $this->subject = $subjectResolver->resolveSubject($this->activity);

        $this->subjectClass = $subjectResolver->resolveSubjectClass($this->activity, $this->subject);

        $record = null;

        try {
            $record = $this->component->getRecord();
        } catch (Throwable) {
            $record = null;
        }

        $this->model = $record instanceof Model ? $record::class : null;

        $this->context = new ActivitylogEntryContext(
            activity: $this->activity,
            component: $this->component,
            subject: $this->subject,
            subjectClass: $this->subjectClass,
            modelClass: $this->model,
            isBatchItem: $this->isGroupEntry,
        );
    }

    public function getDescription(): string | HtmlString
    {
        return app(RenderActivitylogDescriptionAction::class)->execute($this->context);
    }

    public function getRecordTitle(): ?string
    {
        return app(ActivitylogRecordPresenterContract::class)->resolveTitle($this->context);
    }

    public function getCauserName(): ?string
    {
        return app(ActivitylogCauserPresenterContract::class)->resolveName(
            $this->activity,
            $this->activity->causer,
            $this->component,
        );
    }

    public function getCauserUrl(): ?string
    {
        return app(ActivitylogCauserPresenterContract::class)->resolveUrl(
            $this->activity,
            $this->activity->causer,
            $this->component,
        );
    }

    public function getChangesSummary(): ?string
    {
        return app(AssembleActivitylogChangesSummaryAction::class)->execute($this->context);
    }

    public function getAttributeLabel(string $key): string
    {
        return app(FormSchemaLabelResolver::class)->resolve($this->context, $key);
    }

    public function parseAttributeValue(mixed $rawValue, string $key, string $rawAttributePropertyKey = 'attributes'): ?string
    {
        return app(RenderActivitylogAttributeValueAction::class)->execute($this->context, $rawValue, $key, $rawAttributePropertyKey);
    }

    public function getSubjectModelLabel(): ?string
    {
        return app(ActivitylogRecordPresenterContract::class)->resolveSubjectModelLabel($this->context);
    }

    public function getRelationshipName(): ?string
    {
        return app(ActivitylogRecordPresenterContract::class)->resolveRelationshipName($this->context);
    }

    public function getDateTime(): CarbonInterface
    {
        return $this->activity->created_at ?? Carbon::now();
    }

    public function getDateTimeFormat(): string
    {
        return $this->component->getItemDateTimeFormat() ?? 'M jS, Y H:i:s';
    }

    public function getDateTimeTimezone(): ?string
    {
        return $this->component->getItemDateTimeTimezone();
    }

    public function getDateTimeFormatted(): string
    {
        $format = $this->getDateTimeFormat();

        $dateTime = $this->getDateTime();

        if ($timezone = $this->getDateTimeTimezone()) {
            $dateTime->setTimezone($timezone);
        }

        return $dateTime->translatedFormat($format);
    }

    public function getIcon(bool $isCompact = false): string | BackedEnum | null
    {
        $icon = $this->component->getItemIcon($this->activity);

        return app(TimelineIconScaler::class)->forDisplay($icon, $isCompact);
    }

    /**
     * @return null|string|array<int|string, string>
     */
    public function getIconColor(): null | string | array
    {
        return $this->component->getItemIconColor($this->activity);
    }

    /**
     * @return null|string|array<int|string, string>
     */
    public function getBadge(): null | string | array
    {
        return $this->component->getItemBadge($this->activity);
    }

    /**
     * @return null|string|array<int|string, string>
     */
    public function getBadgeColor(): null | string | array
    {
        return $this->component->getItemBadgeColor($this->activity);
    }

    /**
     * @return array<int|string, Action>
     */
    public function getActions()
    {
        return $this->component->getItemActions($this->activity);
    }

    public function getGroupViewData(): ?ActivitylogTimelineViewData
    {
        if ($this->isGroupEntry) {
            return null;
        }

        if (! $this->component->supportsActivityGroups()) {
            return null;
        }

        $groupKey = ActivityBatch::getBatchUuid($this->activity);

        if (! $groupKey) {
            return null;
        }

        if (! $this->component->areGroupActivitiesVisible()) {
            return null;
        }

        if ($this->component->isGroupInline()) {
            return null;
        }

        $groupActivities = $this->component->getGroupActivities($this->activity, $groupKey);

        if ($groupActivities->containsOneItem()) {
            return null;
        }

        return new ActivitylogTimelineViewData(
            timelineEntries: $groupActivities->map(
                fn (ActivityModel $activity) => new ActivitylogTimelineEntry($activity, $this->component, true),
            ),
            evaluationCallback: $this->component->evaluate(...),
            isCompact: true,
            isCollapsible: $this->component->isCollapsible(),
            collapsedVisibleCount: $this->component->getCollapsedVisibleCount(),
        );
    }

    /**
     * @deprecated Use `getDateTime()` instead.
     */
    public function getTime(): CarbonInterface
    {
        return $this->getDateTime();
    }
}
