<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;
use Socialize;
use Auth;
use Socialite;
use App\User;
use App\SocialUser;
class SocialsiteController extends Controller{
    // To redirect github
    public function google_redirect($redirectUrl) {
        \Session::put('corespondence_redirect_url',base64_decode($redirectUrl));
        return Socialize::with('google')->redirect();
    }
    // to get authenticate user data
    public function google() {
       try {
            $user = Socialize::with('google')->user();
        } catch (Exception $e) {
            return redirect(\Session::forget('corespondence_redirect_url'));
        }        
        $authUser = $this->findOrCreateUser($user,'G');
        \Session::put('social_user_id', $authUser->id);
        \Session::put('social_name', $authUser->name);
        \Session::put('social_email', $authUser->email);
        \Session::put('socialsite_id', $authUser->socialsite_id); 
        \Session::put('avatar', $authUser->avatar);  
        \Session::put('avatar_original', $authUser->avatar_original);      
        //Auth::login($authUser, true);
        //return redirect()->route('conference'); 
        $corespondence_redirect_url = \Session::get('corespondence_redirect_url');
        if($corespondence_redirect_url != ''){
            \Session::forget('corespondence_redirect_url');
            return redirect($corespondence_redirect_url);
        }else{
            return redirect('/');
        }
    }
    /**
     * [facebook_redirect description]
     * @return [type] [description]
     */
    public function facebook_redirect($redirectUrl){ 
        \Session::put('corespondence_redirect_url',base64_decode($redirectUrl));
        return Socialite::with('facebook')->redirect();
    }
    /**
     * [facebook description]
     * @return [type] [description]
     */
    public function facebook() { 
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (Exception $e) {
            return redirect(\Session::forget('corespondence_redirect_url'));
        }
        $authUser = $this->findOrCreateUser($user,'F');
        \Session::put('social_user_id', $authUser->id);
        \Session::put('social_name', $authUser->name);
        \Session::put('social_email', $authUser->email);
        \Session::put('socialsite_id', $authUser->socialsite_id); 
        \Session::put('avatar', $authUser->avatar);  
        \Session::put('avatar_original', $authUser->avatar_original);      
        //Auth::login($authUser, true);
        //return redirect()->route('conference'); 
        $corespondence_redirect_url = \Session::get('corespondence_redirect_url');
        if($corespondence_redirect_url != ''){
            \Session::forget('corespondence_redirect_url');
            return redirect($corespondence_redirect_url);
        }else{
            return redirect('/');
        }
    }
    /**
     * [twitter_redirect description]
     * @return [type] [description]
     */
    public function twitter_redirect($redirectUrl){
        \Session::put('corespondence_redirect_url',base64_decode($redirectUrl));
        return Socialite::driver('twitter')->redirect();
    }
    /**
     * [twitter description]
     * @return [type] [description]
     */
    public function twitter() {
        try {
            $user = Socialite::driver('twitter')->user();
        } catch (Exception $e) {
            return redirect(\Session::forget('corespondence_redirect_url'));
        }
 
        $authUser = $this->findOrCreateUser($user,'T');
        \Session::put('social_user_id', $authUser->id);
        \Session::put('social_name', $authUser->name);
        \Session::put('social_email', $authUser->email);
        \Session::put('socialsite_id', $authUser->socialsite_id); 
        \Session::put('avatar', $authUser->avatar);  
        \Session::put('avatar_original', $authUser->avatar_original);      
        //Auth::login($authUser, true);
        //return redirect()->route('conference'); 
        $corespondence_redirect_url = \Session::get('corespondence_redirect_url');
        if($corespondence_redirect_url != ''){
            \Session::forget('corespondence_redirect_url');
            return redirect($corespondence_redirect_url);
        }else{
            return redirect('/');
        }
        //Auth::login($authUser, true);
 
        //return redirect()->route('dashboard');
    } 
    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $twitterUser
     * @return User
     */
    private function findOrCreateUser($user,$social){
        $authUser = SocialUser::where(['socialsite_id'=> $user->id,'socialsite'=>$social])->first();
        if ($authUser){
            return $authUser;
        }
        if(isset($user->avatar_original)){
            $avatar_original = $user->avatar_original;
        }else{
            $avatar_original = '';
        }
        return SocialUser::create([
            'name'              => $user->name,
            'email'             => $user->email,
            'socialsite_id'     => $user->id,
            'socialsite'        => $social,
            'avatar'            => $user->avatar,
            'avatar_original'   => $avatar_original
        ]);
    } 
    public function logout($redirectUrl){
        $url = base64_decode($redirectUrl);
        \Session::flush();
        return redirect($url);
    }
}
