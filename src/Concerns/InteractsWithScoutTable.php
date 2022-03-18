<?php

namespace StikNoltz\FilamentScoutTable\Concerns;

use Filament\Forms;
use Filament\Tables\Table;
use Laravel\Scout\Builder;
use Filament\Tables\Concerns\HasActions;
use Filament\Tables\Concerns\HasBulkActions;
use Filament\Tables\Concerns\HasColumns;
use Filament\Tables\Concerns\HasContentFooter;
use Filament\Tables\Concerns\HasEmptyState;
use Filament\Tables\Concerns\HasHeader;
use Filament\Tables\Concerns\HasRecordUrl;
use Filament\Tables\Concerns\HasRecordAction;
use Filament\Forms\Concerns\InteractsWithForms;

/**
 * @method Builder getTableQuery()
 */
trait InteractsWithScoutTable
{
    use CanPaginateScoutRecords;
    use CanSearchScoutRecords;
    use CanSelectScoutRecords;
    use CanSortScoutRecords;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasContentFooter;
    use HasEmptyState;
    use HasScoutFilters;
    use HasHeader;
    use HasScoutRecords;
    use HasRecordAction;
    use HasRecordUrl;
    use InteractsWithForms;

    protected Table $table;

    public function bootedInteractsWithScoutTable(): void
    {
        $this->table = $this->getTable();

        $this->cacheTableActions();
        $this->cacheTableBulkActions();
        $this->cacheTableEmptyStateActions();
        $this->cacheTableHeaderActions();

        $this->cacheTableColumns();

        $this->cacheTableFilters();
        $this->getTableFiltersForm()->fill($this->tableFilters);
    }

    public function mountInteractsWithScoutTable(): void
    {
        if ($this->isTablePaginationEnabled()) {
            $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
        }

        $this->tableSortColumn ??= $this->getDefaultTableSortColumn();
        $this->tableSortDirection ??= $this->getDefaultTableSortDirection();
    }

    protected function getCachedTable(): Table
    {
        return $this->table;
    }

    protected function getTable(): Table
    {
        return $this->makeTable()
            ->contentFooter($this->getTableContentFooter())
            ->description($this->getTableDescription())
            ->emptyState($this->getTableEmptyState())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->enablePagination($this->isTablePaginationEnabled())
            ->filtersFormWidth($this->getTableFiltersFormWidth())
            ->recordAction($this->getTableRecordAction())
            ->getRecordUrlUsing($this->getTableRecordUrlUsing())
            ->header($this->getTableHeader())
            ->heading($this->getTableHeading())
            ->model($this->getModel())
            ->recordsPerPageSelectOptions($this->getTableRecordsPerPageSelectOptions());
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return null;
    }

    protected function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        if (filled($this->getTableQueryStringIdentifier())) {
            return $this->getTableQueryStringIdentifier() . ucfirst($property);
        }

        return $property;
    }

    protected function getInteractsWithScoutTableForms(): array
    {
        return $this->getTableForms();
    }

    protected function getTableForms(): array
    {
        return [
            'mountedTableActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedTableAction()) ? $action->getFormSchema() : [])
                ->model($this->getMountedTableActionRecord() ?? $this->getModel())
                ->statePath('mountedTableActionData'),
            'mountedTableBulkActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedTableBulkAction()) ? $action->getFormSchema() : [])
                ->model($this->getModel())
                ->statePath('mountedTableBulkActionData'),
            'tableFiltersForm' => $this->makeForm()
                ->schema($this->getTableFiltersFormSchema())
                ->columns($this->getTableFiltersFormColumns())
                ->statePath('tableFilters')
                ->reactive(),
        ];
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
