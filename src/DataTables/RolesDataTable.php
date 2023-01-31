<?php
namespace App\DataTables;
 
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
 
class RolesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))->setRowId('id')
        ->addColumn('action', function($query) {
            return '<div class="d-flex"><a href="'.route('roles.edit',$query->id).'" class="btn btn-sm btn-primary btn-icon item-edit"><i class="fa-solid fa-pen-to-square"></i></a><a data-href="'.route("roles.destroy",$query->id).'" class="mx-2 btn btn-sm btn-danger btn-icon item-edit delete"><i class="fa-solid fa-trash"></i></a></div>';
        });
    }
 
    public function query(): QueryBuilder
    {
        return $this->roles->newQuery();
    }
 
    public function html(): HtmlBuilder
    {
        return $this->builder()
                ->setTableId('roles-table')
                ->columns($this->getColumns())
                ->minifiedAjax()
                ->orderBy(1)
                ->parameters([
                    'paging' => true,
                    'processing'=> true,
                    'serverSide'=> true,
                    'searching' => true,
                    'info' => false,
                    'searchDelay' => 350,
                    'language' => [
                        'url' => url('js/dataTables/language.json')
                    ],
                    "select"=>true,
                    'dom' => 'Bfrtlpi',
                    "buttons" => ["copy", "csv", "excel", "pdf", "print", "colvis"]
                ])
                ->selectStyleSingle()
                ->responsive(true)
                ->autoWidth(100)
                ->addTableClass('table table-bordered table-hover gy-5 gs-7 border  w-100');;
    }
 
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::make('action'),
        ];
    }
 
    protected function filename(): string
    {
        return 'Users_'.date('YmdHis');
    }
}