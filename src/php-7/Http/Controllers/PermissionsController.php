<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Permission\StoreRequest;
use App\Http\Requests\Permission\UpdateRequest;
use DB;
use DataTables;
class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $permissions;

    public function __construct(Permission $permissions)
    {
        $this->permissions = $permissions;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->permissions->get()->toArray();
            $result = [];
            foreach ($query as $k => $value) {
                $result[$value['title']]['name'][] = $value['name'];
                $result[$value['title']]['title'] = $value['title'];
                $result[$value['title']]['guard_name'] = $value['guard_name'];
                $result[$value['title']]['description'][] = $value['description'];
            }
            return Datatables::of($result)
                ->addIndexColumn()
                ->addColumn('action', function ($result) {
                    return '<div class="d-flex"><a href="' . route('permissions.edit', $result['title']) . '" class="btn btn-sm btn-primary btn-icon item-edit"><i class="fa-solid fa-pen-to-square"></i></a><a data-href="' . route('permissions.destroy', $result['title']) . '" class="mx-2 btn btn-sm btn-danger btn-icon item-edit delete"><i class="fa-solid fa-trash"></i></a></div>';
                })
                ->addColumn('name', function ($result) {
                    $list = '<div>';
                    foreach ($result['name'] as $k => $value) {
                        $list .= "<p class='my-2'>" . ($k + 1) . ".${value}</p>";
                    }
                    $list .= '</div>';
                    return $list;
                })
                ->addColumn('description', function ($result) {
                    $list = '<div>';
                    foreach ($result['description'] as $k => $value) {
                        $list .= "<p class='my-2'>" . ($k + 1) . ".${value}</p>";
                    }
                    $list .= '</div>';
                    return $list;
                })
                ->rawColumns(['name', 'description', 'action'])
                ->make(true);
        }
        return view('admin.permissions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $insert_data = [];
            $insert_data['title'] = $validated['title'];
            foreach ($validated['name'] as $k => $value) {
                $insert_data['name'] = $value;
                $insert_data['description'] = $validated['description'][$k];
            }
            $this->permissions->create($insert_data);
            DB::commit();

            drakify('success');
            return redirect()
                ->route('permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (Exception $e) {
            drakify('error');
            DB::rollback();
            return redirect()
                ->back()
                ->withError('Try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $title = $id;
            $permissions = $this->permissions
                ->where('title', $id)
                ->get()
                ->toArray();
            if ($permissions) {
                return view('admin.permissions.edit', compact('permissions', 'title'));
            }
        } catch (Exception $e) {
            drakify('error');
            return redirect()
                ->back()
                ->withError('Try again');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, $id)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $insert_data = [];
            $insert_data['title'] = $validated['title'];
            $update_id = array_keys($validated['name']);
            $this->permissions
                ->whereNotIn('id', $update_id)
                ->where('title', $validated['title'])
                ->delete();
            foreach ($validated['name'] as $k => $value) {
                $insert_data['name'] = $value;
                $insert_data['description'] = $validated['description'][$k];
                $permissions = $this->permissions
                    ->where('id', $k)
                    ->where('title', $validated['title'])
                    ->first();
                if ($permissions) {
                    $permissions->update($insert_data);
                } else {
                    $this->permissions->create($insert_data);
                }
            }

            DB::commit();

            drakify('success');

            return redirect()
                ->route('permissions.index')
                ->with('success', 'Record updated successfully.');
        } catch (Exception $e) {
            DB::rollback();
            drakify('error');
            return redirect()
                ->back()
                ->withError('Try again');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = $this->permissions->where('title', $id);
        $data['status'] = false;
        if ($permission) {
            $permission->delete();
            drakify('success');
            $data['status'] = true;
            return $data;
        } else {
            drakify('error');
            return $data;
        }
    }
}
