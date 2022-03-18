<?php

namespace StikNoltz\FilamentScoutTable\Filters\Concerns;

use Closure;
use Laravel\Scout\Builder;

trait InteractsWithScoutQuery
{
    protected ?Closure $modifyQueryUsing = null;

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->hasQueryModificationCallback()) {
            return $query;
        }

        if (! ($data['isActive'] ?? true)) {
            return $query;
        }

        $callback = $this->modifyQueryUsing;
        $callback($query, $data);

        return $query;
    }

    public function query(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    protected function hasQueryModificationCallback(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }
}
