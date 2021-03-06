<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;

trait HasScoutRecords
{
    protected Collection | Paginator | null $records = null;

    protected function getFilteredTableQuery(): Builder
    {
        $searchQuery = $this->getTableSearchQuery();

        $query = $this->getTableQuery($searchQuery);

        $this->applyFiltersToTableQuery($query);

        return $query;
    }

    public function getTableRecords(): Collection | Paginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        $this->applySortingToTableQuery($query);

        $this->records = $this->isTablePaginationEnabled() ?
            $this->paginateTableQuery($query) :
            $query->get();

        return $this->records;
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        return $this->getTableQuery()->get()->find($key);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->getKey();
    }
}
