<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
        
            $user = Socialite::driver('facebook')->stateless()->user();
         
            $finduser = User::where('oauth_id', $user->id)->where('oauth_type','facebook')->first();
         
            if($finduser){
         
                Auth::login($finduser);
        
                return redirect()->intended('home');
         
            }else{

                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'oauth_id'=> $user->id,
                        'oauth_type' => 'facebook',
                        'password' => Hash::make($user->id)
                    ]);
                    
                $newUser->assignRole('user');

                Auth::login($newUser);
        
                return redirect()->intended('home');

            }
        
        } catch (Exception $e) {

            dd($e->getMessage());
            
        }
    }
}
