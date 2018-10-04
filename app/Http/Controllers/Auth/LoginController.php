<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Socialite;
use App\User;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $user_social = Socialite::driver('facebook')->user();

        // $user_social->token;
        try {
            
            $user = User::where('email', $user_social->getEmail())->firstOrFail();

            Auth::login($user, true);

        } catch (ModelNotFoundException $e) {

            $user = User::create([
                'name' => $user_social->getName(),
                'email' => $user_social->getEmail(),
                'password' => bcrypt($user_social->token), // FIXME
            ]);

            Auth::login($user, true);

        } catch (Exception $e) {

            return redirect('/login/facebook');
            
        }

        return redirect($this->redirectTo);
        
    }
}
