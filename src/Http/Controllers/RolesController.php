<?php

namespace App\Http\Controllers;

use DB;
use Arr;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\DataTables\RolesDataTable;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $roles;

    public function __construct(Role $roles){
        $this->roles = $roles;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RolesDataTable $dataTable)
    {   
        return $dataTable->with('roles', $this->roles)->render('admin.roles.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::get();

        $permissions_array = [];
        foreach($permissions as $permission){
            $permissions_array[$permission->title][] = $permission;
        }

        return view('admin.roles.create', compact('permissions_array'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission*' => 'required',
        ]);
    
        $role = $this->roles->create(['name' => $request->get('name')]);
        $role->syncPermissions($request->get('permission'));
    
        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role = $role;
        $rolePermissions = $role->permissions;
    
        return view('roles.show', compact('role', 'rolePermissions'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roles->find($id);

        $rolePermissions = $role->permissions->toArray();

        $parent_array = array_count_values(array_column($rolePermissions,'title'));
        $rolePermissions_array = array_column($rolePermissions,'name');
        $permissions = Permission::get();

        $total_count = count($permissions);

        $permissions_array = [];
        foreach($permissions as $permission){
            $permissions_array[$permission->title][] = $permission;
        }
    
        return view('admin.roles.edit', compact('role', 'rolePermissions_array', 'parent_array','permissions_array','total_count'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission*' => 'required',
        ]);
       
        $role = $this->roles->find($id)->update($request->only('name'));
    
        $role = Role::where('id',$id)->first();
        $role->syncPermissions($request->get('permission'));
    
        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->roles->find($id);
        $data['status'] = false;
        if($role){

            $role->delete();
            drakify('success') ;
            $data['status'] = true;
            return $data;

        }else{

            drakify('error');
            return $data;
                    
        }
    }
}
