<?php

/**
 * @internal
 */

namespace Syriable\Filament\Plugins\Activitylog\Support;

use Illuminate\Database\Eloquent\Model;

class SubjectModelCache
{
    /**
     * @var array<class-string<Model>, Model>
     */
    protected array $prototypes = [];

    public function for(?string $subjectClass): ?Model
    {
        if (! $subjectClass || ! is_subclass_of($subjectClass, Model::class)) {
            return null;
        }

        if (! isset($this->prototypes[$subjectClass])) {
            $this->prototypes[$subjectClass] = new $subjectClass();
        }

        return $this->prototypes[$subjectClass];
    }
}
