<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Auth\Events\Registered;
use App\Actions\Contracts\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\StatefulGuard;

class RegisterController extends Controller 
{

    protected $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
        
    }

    public function show(){
        return view('auth.register');
    } 


    public function store(Request $request,CreateNewUser $creator)
    {
        event(new Registered($user = $creator->create($request->all())));

        $this->guard->login($user);
        
        $user->assignRole('user');
        
        $request->session()->regenerate();

         return redirect()->intended('home')
            ->withSuccess('Signed in');
    }

}