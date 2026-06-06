<?php

namespace Syriable\Filament\Plugins\Activitylog\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;

class ActivitylogTimelineAction extends Action
{
    protected ?Closure $modifyActivitylogTimelineCallback = null;

    public static function getDefaultName(): ?string
    {
        return 'activities';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-o-bars-arrow-down')
            ->slideOver()
            ->modalWidth(Width::ExtraLarge)
            ->modalSubmitAction(fn (Action $action) => $action->hidden())
            ->label(__('filament-activitylog::translations.actions.timeline-action.label'))
            ->modalCancelActionLabel(__('filament-activitylog::translations.actions.timeline-action.modal_cancel_action_label'))
            ->mountUsing(function (Model $record, Schema $schema) {
                $schema->record($record);
            })
            ->schema(fn (Schema $schema) => [
                ActivitylogTimeline::make()
                    ->when($this->hasModifyActivitylogTimelineCallback(), function (ActivitylogTimeline $timeline) use ($schema) {
                        return $schema->evaluate(
                            $this->getModifyActivitylogTimelineCallback(),
                            namedInjections: [
                                'timeline' => $timeline,
                            ],
                            typedInjections: [
                                $timeline::class => $timeline,
                            ]
                        );
                    }),
            ]);
    }

    public function modifyActivitylogTimelineUsing(Closure $callback): static
    {
        $this->modifyActivitylogTimelineCallback = $callback;

        return $this;
    }

    public function hasModifyActivitylogTimelineCallback(): bool
    {
        return $this->modifyActivitylogTimelineCallback !== null;
    }

    public function getModifyActivitylogTimelineCallback(): ?Closure
    {
        return $this->modifyActivitylogTimelineCallback;
    }
}
