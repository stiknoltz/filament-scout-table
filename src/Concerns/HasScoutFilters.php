<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Laravel\Scout\Builder;
use StikNoltz\FilamentScoutTable\Filters\ScoutFilter;

/**
 * @property ComponentContainer $tableFiltersForm
 */
trait HasScoutFilters
{
    protected array $cachedTableFilters;

    public $tableFilters = null;

    public function cacheTableFilters(): void
    {
        $this->cachedTableFilters = collect($this->getTableFilters())
            ->mapWithKeys(function (Filter | ScoutFilter $filter): array {
                $filter->table($this->getCachedTable());

                return [$filter->getName() => $filter];
            })
            ->toArray();
    }

    public function getCachedTableFilters(): array
    {
        return collect($this->cachedTableFilters)
            ->filter(fn (Filter | ScoutFilter $filter): bool => ! $filter->isHidden())
            ->toArray();
    }

    public function getCachedTableFilter(string $name): ?BaseFilter
    {
        return $this->getCachedTableFilters()[$name] ?? null;
    }

    public function getTableFiltersForm(): Forms\ComponentContainer
    {
        if ((! $this->isCachingForms) && $this->hasCachedForm('tableFiltersForm')) {
            return $this->getCachedForm('tableFiltersForm');
        }

        return $this->makeForm()
            ->schema($this->getTableFiltersFormSchema())
            ->columns($this->getTableFiltersFormColumns())
            ->statePath('tableFilters')
            ->reactive();
    }

    public function isTableFilterable(): bool
    {
        return (bool) count($this->getCachedTableFilters());
    }

    public function updatedTableFilters(): void
    {
        if ($this->shouldPersistTableFiltersInSession()) {
            session()->put(
                $this->getTableFiltersSessionKey(),
                $this->tableFilters,
            );
        }

        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    public function removeTableFilter(string $filter, ?string $field = null): void
    {
        $filterGroup = $this->getTableFiltersForm()->getComponents()[$filter];
        $fields = $filterGroup?->getChildComponentContainer()->getFlatFields() ?? [];

        if (filled($field) && array_key_exists($field, $fields)) {
            $fields = [$fields[$field]];
        }

        foreach ($fields as $field) {
            $state = $field->getState();

            $field->state(match (true) {
                is_array($state) => [],
                $state === true => false,
                default => null,
            });
        }

        $this->updatedTableFilters();
    }

    public function removeTableFilters(): void
    {
        foreach ($this->getTableFiltersForm()->getFlatFields(withAbsolutePathKeys: true) as $field) {
            $state = $field->getState();

            $field->state(match (true) {
                is_array($state) => [],
                $state === true => false,
                default => null,
            });
        }

        $this->updatedTableFilters();
    }

    public function resetTableFiltersForm(): void
    {
        $this->getTableFiltersForm()->fill();

        $this->getTableFiltersForm()->fill();
    }

    protected function applyFiltersToTableQuery(Builder $query): Builder
    {
        $data = $this->getTableFiltersForm()->getState();

        foreach ($this->getCachedTableFilters() as $filter) {
            $filter->apply(
                $query,
                $data[$filter->getName()] ?? [],
            );
        }

        return $query;
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableFiltersFormColumns(): int | array
    {
        return match ($this->getTableFiltersLayout()) {
            Layout::AboveContent, Layout::BelowContent => [
                'sm' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ],
            default => 1,
        };
    }

    public function getTableFilterState(string $name): ?array
    {
        return $this->getTableFiltersForm()->getRawState()[$this->parseFilterName($name)] ?? null;
    }

    public function parseFilterName(string $name): string
    {
        if (! class_exists($name)) {
            return $name;
        }

        if (! is_subclass_of($name, BaseFilter::class)) {
            return $name;
        }

        return $name::getDefaultName();
    }

    protected function getTableFiltersFormSchema(): array
    {
        return array_map(
            fn (ScoutFilter | Filter $filter) => Forms\Components\Group::make()
                ->schema($filter->getFormSchema())
                ->statePath($filter->getName()),
            $this->getCachedTableFilters(),
        );
    }

    protected function getTableFiltersFormWidth(): ?string
    {
        return match ($this->getTableFiltersFormColumns()) {
            2 => '2xl',
            3 => '4xl',
            4 => '6xl',
            default => null,
        };
    }
}
