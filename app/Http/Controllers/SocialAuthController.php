<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Socialite;

use App\SocialAccountService;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->scopes([
            'public_profile','email'
        ])->redirect();
    }   

    public function callback(SocialAccountService $service, Request $request)
    {

        $state = $request->get('state');
           $request->session()->put('state',$state);

        if(\Auth::check()==false){
          session()->regenerate();
        }
      
        $user = $service->createOrGetUser(Socialite::driver('facebook')->user());

        try{
			auth()->login($user);
		} 
		catch(\Exception $e){
		    return "can't log in";
		}

		return $user = auth()->user();
    }
}
