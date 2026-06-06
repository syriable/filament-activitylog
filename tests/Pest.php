<?php

use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

beforeEach(function () {
    ActivitylogTimeline::activityGroups(null);
});
