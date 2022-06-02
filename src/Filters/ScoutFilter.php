<?php

namespace StikNoltz\FilamentScoutTable\Filters;

use Filament\Support\Components\Component;
use Filament\Tables\Filters\Concerns\BelongsToTable;
use Filament\Tables\Filters\Concerns\CanBeHidden;
use Filament\Tables\Filters\Concerns\HasDefaultState;
use Filament\Tables\Filters\Concerns\HasFormSchema;
use Filament\Tables\Filters\Concerns\HasLabel;
use Filament\Tables\Filters\Concerns\HasName;
use Illuminate\Support\Traits\Conditionable;

class ScoutFilter extends Component
{
    use BelongsToTable;
    use HasDefaultState;
    use CanBeHidden;
    use HasFormSchema;
    use HasLabel;
    use HasName;
    use Concerns\InteractsWithScoutQuery;
    use Conditionable;

    protected string $evaluationIdentifier = 'filter';

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

    protected function getDefaultEvaluationParameters(): array
    {
        return array_merge(parent::getDefaultEvaluationParameters(), [
            'livewire' => $this->getLivewire(),
        ]);
    }
}
