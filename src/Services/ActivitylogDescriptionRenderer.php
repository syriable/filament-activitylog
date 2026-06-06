<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Services;

use Illuminate\Support\HtmlString;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogChangesSummaryAssemblerContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogDescriptionRendererContract;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogRecordPresenterContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Enums\LoggedEventKind;

class ActivitylogDescriptionRenderer implements ActivitylogDescriptionRendererContract
{
    public function __construct(
        protected ActivitylogCauserPresenter $causerResolver,
        protected ActivitylogRecordPresenterContract $recordResolver,
        protected ActivitylogChangesSummaryAssemblerContract $changesSummaryBuilder,
    ) {}

    public function format(ActivitylogEntryContext $context): string | HtmlString
    {
        $recordTitle = e($this->recordResolver->resolveTitle($context));
        $causerName = e($this->causerResolver->resolveName($context->activity, $context->activity->causer, $context->component));
        $changesSummary = $this->changesSummaryBuilder->build($context);

        if ($description = $context->component->getEventDescription($context->activity, $causerName, $changesSummary)) {
            if (is_string($description)) {
                $description = e($description);
            }

            if ($context->component->hasModifyEventDescriptionCallback()) {
                $description = $context->component->modifyEventDescription($context->activity, $description, $recordTitle, $causerName, $changesSummary);
            }

            if (is_string($description)) {
                $description = str(e($description))->inlineMarkdown()->toHtmlString();
            }

            return $description;
        }

        if (($description = $context->activity->description) && $context->activity->description !== $context->activity->event) {
            if ($context->component->hasModifyEventDescriptionCallback()) {
                $description = $context->component->modifyEventDescription($context->activity, $description, $recordTitle, $causerName, $changesSummary);
            }

            return str(e($description))->inlineMarkdown()->toHtmlString();
        }

        if (! $context->subject) {
            $description = $this->generateEventDescription($context, $context->activity->event ?? 'custom');

            if ($context->component->hasModifyEventDescriptionCallback()) {
                $description = $context->component->modifyEventDescription($context->activity, $description, $recordTitle, $causerName, $changesSummary);
            }

            if (is_string($description)) {
                $description = str(e($description))->inlineMarkdown()->toHtmlString();
            }

            return $description;
        }

        $description = match ($context->activity->event) {
            LoggedEventKind::Created->value => $this->generateEventDescription($context, LoggedEventKind::Created->value),
            LoggedEventKind::Updated->value => $this->generateEventDescription($context, LoggedEventKind::Updated->value),
            LoggedEventKind::Deleted->value => $this->generateEventDescription($context, LoggedEventKind::Deleted->value),
            LoggedEventKind::Restored->value => $this->generateEventDescription($context, LoggedEventKind::Restored->value),
            null => $context->activity->description,
            default => $this->generateEventDescription($context, $context->activity->event),
        };

        if ($context->component->hasModifyEventDescriptionCallback()) {
            $description = $context->component->modifyEventDescription($context->activity, $description, $recordTitle, $causerName, $changesSummary);
        }

        if (is_string($description)) {
            $description = str(e($description))->inlineMarkdown()->toHtmlString();
        }

        return $description;
    }

    protected function generateEventDescription(ActivitylogEntryContext $context, string $event): string
    {
        $formattedEvent = str($event)->headline()->lower();

        $causerName = e($this->causerResolver->resolveName($context->activity, $context->activity->causer, $context->component));

        if ($causerUrl = $this->causerResolver->resolveUrl($context->activity, $context->activity->causer, $context->component)) {
            $causerName = "[{$causerName}]({$causerUrl})";
        }

        $replace = [
            'causerName' => $causerName,
            'changesSummary' => $context->subjectClass ? $this->changesSummaryBuilder->build($context) : null,
            'modelLabel' => $context->subjectClass ? e($this->recordResolver->resolveSubjectModelLabel($context)) : null,
            'relationshipName' => $context->subjectClass && $context->modelClass && $context->modelClass !== $context->subjectClass
                ? e($this->recordResolver->resolveRelationshipName($context))
                : null,
            'event' => $formattedEvent,
        ];

        if ($replace['relationshipName'] && $context->subject) {
            $replace['relatedRecordTitle'] = e($this->recordResolver->resolveTitle($context));

            if ($relatedRecordUrl = $this->recordResolver->resolveRecordUrl($context)) {
                $relatedRecordUrl = e($relatedRecordUrl);

                $replace['relatedRecordTitle'] = "[{$replace['relatedRecordTitle']}]({$relatedRecordUrl})";
            }
        }

        $replace['relatedRecordTitle'] ??= null;

        $hasCauser = $replace['causerName'] !== '';
        $hasChangesSummary = $replace['changesSummary'] !== null;
        $hasRelationship = $replace['relationshipName'] !== null;
        $hasRelatedRecordTitle = $replace['relatedRecordTitle'] !== null;

        if (LoggedEventKind::tryFromEvent($event)) {
            $eventTranslationKey = $event;
        } elseif (! $context->subjectClass) {
            $eventTranslationKey = 'no-subject';
        } else {
            $eventTranslationKey = 'custom';
        }

        if ($event === LoggedEventKind::Updated->value) {
            return match (true) {
                $hasCauser && $hasChangesSummary && ! $hasRelationship => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-changes-summary', $replace),
                $hasCauser && $hasChangesSummary && $hasRelationship && ! $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-changes-summary-relationship', $replace),
                $hasCauser && $hasChangesSummary && $hasRelationship && $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-changes-summary-relationship-related-record-title', $replace),
                $hasCauser && ! $hasChangesSummary && ! $hasRelationship => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-without-changes-summary', $replace),
                $hasCauser && ! $hasChangesSummary && $hasRelationship && ! $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-without-changes-summary-relationship', $replace),
                $hasCauser && ! $hasChangesSummary && $hasRelationship && $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.causer-without-changes-summary-relationship-related-record-title', $replace),
                ! $hasCauser && $hasChangesSummary && ! $hasRelationship => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-changes-summary', $replace),
                ! $hasCauser && $hasChangesSummary && $hasRelationship && ! $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-changes-summary-relationship', $replace),
                ! $hasCauser && $hasChangesSummary && $hasRelationship && $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-changes-summary-relationship-related-record-title', $replace),
                ! $hasCauser && ! $hasChangesSummary && ! $hasRelationship => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-without-changes-summary', $replace),
                ! $hasCauser && ! $hasChangesSummary && $hasRelationship && ! $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-without-changes-summary-relationship', $replace),
                ! $hasCauser && ! $hasChangesSummary && $hasRelationship && $hasRelatedRecordTitle => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-without-changes-summary-relationship-related-record-title', $replace),
                default => __('filament-activitylog::translations.activity-timeline-item.event-descriptions.updated.without-causer-without-changes-summary', $replace),
            };
        }

        if ($hasCauser) {
            if (! $hasRelationship) {
                return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.causer", $replace);
            }

            if (! $hasRelatedRecordTitle) {
                return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.causer-relationship", $replace);
            }

            return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.causer-relationship-related-record-title", $replace);
        }

        if (! $hasRelationship) {
            return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.without-causer", $replace);
        }

        if (! $hasRelatedRecordTitle) {
            return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.without-causer-relationship", $replace);
        }

        return __("filament-activitylog::translations.activity-timeline-item.event-descriptions.{$eventTranslationKey}.without-causer-relationship-related-record-title", $replace);
    }
}
