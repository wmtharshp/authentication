<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Auth\Events\Registered;
use App\Actions\Contracts\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Helper\EmailActivation;
use App\Models\Activation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
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
        
        $activationEmail = new EmailActivation();
        $activationEmail->createTokenAndSendEmail($user);

        $user->assignRole('user');
        
        $request->session()->regenerate();

        notify()->success('Record updated successfully. ⚡️');

        return redirect()->intended('login')
            ->withSuccess('Signed in');
    }

    public function emailVerify($token){
        $user = Activation::with('user')->where('token',$token)->first();
        if($user){
            return view("auth.verify",compact('user'));
        }else{
            return redirect()->intended('login')
            ->withSuccess('Signed in');
        } 
    }

    public function active(Request $request){

        if ( captcha_check($request->captcha) == false ) {
            return back()->with('error','incorrect captcha!');
        }
    
        $user = User::findOrFail($request->user_id);

        $user->update([
            'email_verified_at' => now(),
        ]);

        $user->refresh();

        // $this->guard->login($user);

        Activation::where('id',$request->token_id)->delete();

        $request->session()->regenerate();
        notify()->success('Account Verify Successfully. ⚡️');
        return redirect()->intended('login');
    }

}