<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait CanSelectScoutRecords
{
    public array $selectedTableRecords = [];

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function getAllTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        return $query->pluck($query->getModel()->getKeyName())->toArray();
    }

    public function getAllTableRecordsCount(): int
    {
        return $this->getFilteredTableQuery()->get()->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        return $this->getTableQuery()->find($this->selectedTableRecords);
    }

    public function isTableSelectionEnabled(): bool
    {
        return (bool) count($this->getCachedTableBulkActions());
    }
}
