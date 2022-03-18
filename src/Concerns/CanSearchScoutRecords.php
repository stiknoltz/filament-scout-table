<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Laravel\Scout\Builder;

trait CanSearchScoutRecords
{
    public $tableSearchQuery = '';

    public function isTableSearchable(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isSearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function updatedTableSearchQuery(): void
    {
        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $searchQuery = $this->getTableSearchQuery();

        if ($searchQuery === '') {
            return $query;
        }

        $query->search($searchQuery);

        return $query;
    }

    protected function getTableSearchQuery(): string
    {
        return trim(strtolower($this->tableSearchQuery));
    }
}
