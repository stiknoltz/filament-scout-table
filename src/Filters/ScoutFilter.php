<?php

namespace StikNoltz\FilamentScoutTable\Filters;

use Filament\Tables\Filters\Concerns\BelongsToTable;
use Filament\Tables\Filters\Concerns\CanBeHidden;
use Filament\Tables\Filters\Concerns\EvaluatesClosures;
use Filament\Tables\Filters\Concerns\HasDefaultState;
use Filament\Tables\Filters\Concerns\HasFormSchema;
use Filament\Tables\Filters\Concerns\HasLabel;
use Filament\Tables\Filters\Concerns\HasName;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class ScoutFilter
{
    use BelongsToTable;
    use HasDefaultState;
    use CanBeHidden;
    use EvaluatesClosures;
    use HasFormSchema;
    use HasLabel;
    use HasName;
    use Concerns\InteractsWithScoutQuery;
    use Conditionable;
    use Macroable;
    use Tappable;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
    }
}
