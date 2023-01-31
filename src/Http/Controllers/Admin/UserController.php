<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use Spatie\Permission\Models\Role;

class UserController extends Controller 
{

    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index(UsersDataTable $dataTable){
        return $dataTable->with('user', $this->user)->render('admin.users.index');
    }
    
    public function create(){
        $roles = Role::all();
        return view('admin.users.create',compact('roles'));
    }

    public function store(StoreRequest $request){

        $validated = $request->safe()->only(['name', 'email','password']);
        $validated['password'] = Hash::make($validated['password']);
      
        DB::beginTransaction();
        try {

            $user = $this->user->create($validated);
            $user->syncRoles($request->get('role'));
            DB::commit();

            // notify()->success('Record created successfully. ⚡️');
            drakify('success') ;
            return redirect()->route('users.index')->with("success","Record created successfully.");

        }catch (Exception $e) {
            drakify('error');
            DB::rollback();
            return redirect()->back()
                    ->withError('Try again');
        }

    }

    public function edit($id){
        try {

            $user = $this->user->find($id);
            $userRole = $user->roles->pluck('name')->toArray();
            $roles = Role::all();
            if($user){
                return view("admin.users.edit",compact('user','roles','userRole'));
            }

        }catch (Exception $e) {
            drakify('error');
            return redirect()->back()
                    ->withError('Try again');
            
        }
    }

    public function update(UpdateRequest $request,$id){
        $validated = $request->safe()->only(['name', 'email']);
        DB::beginTransaction();
        try {

            $user = $this->user->find($id);
            $user->update($validated);
            $user->syncRoles($request->get('role'));
            DB::commit();
             //Toast Message when new user register
                notify()->success('Record updated successfully. ⚡️');
                // drakify('success') ;
               
            return redirect()->route('users.index')->with("success","Record updated successfully.");

        }catch (Exception $e) {
            DB::rollback();
            drakify('error');
            return redirect()->back()
                    ->withError('Try again');
            
        }  
    }
    
    public function destroy($id){
        $user = $this->user->find($id);
        $data['status'] = false;
        if($user){

            $user->delete();
            drakify('success') ;
            $data['status'] = true;
            return $data;

        }else{
            drakify('error');
            return $data;
                    
        }
    }
}