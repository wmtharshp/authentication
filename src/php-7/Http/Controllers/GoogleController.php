<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
        
            $user = Socialite::driver('google')->stateless()->user();
         
            $finduser = User::where('oauth_id', $user->id)->where('oauth_type','google')->first();
         
            if($finduser){
         
                Auth::login($finduser);
        
                return redirect()->intended('home');
         
            }else{

                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'oauth_id'=> $user->id,
                        'oauth_type' => 'google',
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
