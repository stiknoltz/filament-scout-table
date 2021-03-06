<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Laravel\Scout\Builder;

trait CanSortScoutRecords
{
    public $tableSortColumn = null;

    public $tableSortDirection = null;

    public function sortTable(?string $column = null): void
    {
        if ($column === $this->tableSortColumn) {
            if ($this->tableSortDirection === 'asc') {
                $direction = 'desc';
            } elseif ($this->tableSortDirection === 'desc') {
                $column = null;
                $direction = null;
            } else {
                $direction = 'asc';
            }
        } else {
            $direction = 'asc';
        }

        $this->tableSortColumn = $column;
        $this->tableSortDirection = $direction;

        $this->updatedTableSort();
    }

    public function getTableSortColumn(): ?string
    {
        return $this->tableSortColumn;
    }

    public function getTableSortDirection(): ?string
    {
        return $this->tableSortDirection;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return null;
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return null;
    }

    public function updatedTableSort(): void
    {
        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        $columnName = $this->tableSortColumn;

        if (! $columnName) {
            return $query;
        }

        $direction = $this->tableSortDirection ?? 'asc';

        $column = $this->getCachedTableColumn($columnName);

        if (! $column) {
            return $query->orderBy($columnName, $direction);
        }

        $column->applySort($query, $direction);

        return $query;
    }
}
