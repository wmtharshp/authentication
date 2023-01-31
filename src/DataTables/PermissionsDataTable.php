<?php
namespace App\DataTables;
 
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\DataTables;

class PermissionsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query)
    {
        $query = $query->get()->toArray();
        // dd($query);
        $result = array();
        foreach($query as $k => $value){
            $result[$value['title']]['name'][] = $value['name'];
            $result[$value['title']]['title'] = $value['title'];
            $result[$value['title']]['guard_name'] = $value['guard_name'];
            $result[$value['title']]['description'][] = $value['description'];
        }
        // dd($result);
        return DataTables::of($result)->addColumn('action', function ($result) {
            return '<div class="d-flex"><a href="'.route('permissions.edit',$result['title']).'" class="btn btn-sm btn-primary btn-icon item-edit"><i class="fa-solid fa-pen-to-square"></i></a><a data-href="'.route("permissions.destroy",$result['title']).'" class="mx-2 btn btn-sm btn-danger btn-icon item-edit delete"><i class="fa-solid fa-trash"></i></a></div>';
        })->addColumn('name', function ($result) {
            $list = '<div>';
            foreach($result['name'] as $k => $value){
                $list .= "<p class='my-2'>".($k+1).".${value}</p>";
            }
            $list .= '</div>';
            return $list;
        })->addColumn('description', function ($result) {
            $list = '<div>';
            foreach($result['description'] as $k => $value){
                $list .= "<p class='my-2'>".($k+1).".${value}</p>";
            }
            $list .= '</div>';
            return $list;
        })->rawColumns(['name','description','action']);

        return (new DataTable($result))->setRowId('id')
        ->addColumn('action', function($result) {
            return '<div class="d-flex"><a href="'.route('permissions.edit',$result['title']).'" class="btn btn-sm btn-primary btn-icon item-edit"><i class="fa-solid fa-pen-to-square"></i></a><a data-href="'.route("permissions.destroy",$result['title']).'" class="mx-2 btn btn-sm btn-danger btn-icon item-edit delete"><i class="fa-solid fa-trash"></i></a></div>';
        });
    }
 
    public function query(): QueryBuilder
    {
        return $this->permissions->newQuery();
    }
 
    public function html(): HtmlBuilder
    {
        return $this->builder()
                ->setTableId('permissions-table')
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
            Column::make('title'),
            Column::make('name'),
            Column::make('description'),
            Column::make('guard_name'),
            Column::make('action'),
        ];
    }
 
    protected function filename(): string
    {
        return 'Users_'.date('YmdHis');
    }
}