<?php

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Syriable\Filament\Plugins\Activitylog\Contracts\ActivitylogDescriptionRendererContract;
use Syriable\Filament\Plugins\Activitylog\DTOs\ActivitylogEntryContext;
use Syriable\Filament\Plugins\Activitylog\Filament\Infolists\Components\ActivitylogTimeline;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Comment;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\Post;
use Syriable\Filament\Plugins\Activitylog\Tests\Fixtures\User;

it('formats a created event without causer', function () {
    $subject = new class extends Model
    {
        protected $table = 'posts';

        protected $guarded = [];
    };

    $activity = new Activity([
        'event' => 'created',
        'description' => 'created',
    ]);

    $activity->setRelation('subject', $subject);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $subject,
        subjectClass: $subject::class,
        modelClass: $subject::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description)
        ->toBeInstanceOf(\Illuminate\Support\HtmlString::class);

    expect($description->toHtml())
        ->toContain('was created');
});

it('formats an updated event with changes summary', function () {
    $subject = new class extends Model
    {
        protected $table = 'posts';

        protected $guarded = [];

        protected function casts(): array
        {
            return [
                'title' => 'string',
            ];
        }
    };

    $activity = new Activity([
        'event' => 'updated',
        'description' => 'updated',
        'attribute_changes' => [
            'attributes' => ['title' => 'New title'],
            'old' => ['title' => 'Old title'],
        ],
    ]);

    $activity->setRelation('subject', $subject);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $subject,
        subjectClass: $subject::class,
        modelClass: $subject::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('was updated by setting');
});

it('formats a created event with causer', function () {
    $user = User::make([
        'name' => 'John Smith',
    ]);

    $post = Post::make([
        'title' => 'Test post',
    ]);

    $activity = new Activity([
        'event' => 'created',
        'description' => 'created',
    ]);

    $activity->setRelation('subject', $post);
    $activity->setRelation('causer', $user);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('John Smith')
        ->toContain('created');
});

it('formats a relationship activity description', function () {
    $comment = Comment::make([
        'body' => 'A comment',
    ]);

    $activity = new Activity([
        'event' => 'created',
        'description' => 'created',
    ]);

    $activity->setRelation('subject', $comment);

    $timeline = ActivitylogTimeline::make()
        ->modelLabel(Comment::class, 'comment');

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $comment,
        subjectClass: Comment::class,
        modelClass: Post::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('related')
        ->toContain('comment');
});

it('formats a deleted event without causer', function () {
    $post = Post::make([
        'title' => 'Test post',
    ]);

    $activity = new Activity([
        'event' => 'deleted',
        'description' => 'deleted',
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('was deleted');
});

it('formats a restored event without causer', function () {
    $post = Post::make([
        'title' => 'Test post',
    ]);

    $activity = new Activity([
        'event' => 'restored',
        'description' => 'restored',
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make();

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('was restored');
});

it('uses a custom event description when configured', function () {
    $post = Post::make([
        'title' => 'Test post',
    ]);

    $activity = new Activity([
        'event' => 'published',
        'description' => 'published',
    ]);

    $activity->setRelation('subject', $post);

    $timeline = ActivitylogTimeline::make()
        ->eventDescription('published', 'The post was published.');

    $context = new ActivitylogEntryContext(
        activity: $activity,
        component: $timeline,
        subject: $post,
        subjectClass: Post::class,
        modelClass: Post::class,
    );

    $description = app(ActivitylogDescriptionRendererContract::class)->format($context);

    expect($description->toHtml())
        ->toContain('The post was published.');
});
