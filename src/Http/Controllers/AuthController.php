<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Auth\StatefulGuard;
use Auth;
use App\Models\Activation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helper\EmailActivation;
use  App\Actions\Contracts\PasswordValidationRules;
class AuthController extends Controller
{
    use PasswordValidationRules;
    protected $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function show()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()
                ->intended('home')
                ->withSuccess('Signed in');
        }

        return redirect('login')->withSuccess('Login details are not valid');
    }

    public function destroy(Request $request)
    {
        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended('login');
    }

    public function forgot()
    {
        return view('auth.forgot');
    }

    public function generatePasswordLink(Request $request)
    {
        request()->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email',$request->email)->first();
        $activationEmail = new EmailActivation();
        $activationEmail->createNewPassword($user);

        return redirect()->back();
    }

    public function resetPassword($token){
        $user = Activation::with('user')->where('token',$token)->first();
        if($user){
            return view("auth.reset",compact('user','token'));
        }else{
            return redirect()->intended('login');
        } 
    }

    public function changePassword(Request $request){
        request()->validate([
            'password' => $this->passwordRules(),
        ]);

        $user = Activation::with('user')->where('token',$request->token_value)->first();

        if($user){
            User::where('id',$user->user_id)
                ->update([
                    'password' => Hash::make($request->password),
                ]);
        }
        return redirect()->intended('login');
    }
}
