<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        session()->flash('success', 'You are logged in!');
        return $this->redirectTo;
    }
    protected function username()
    {
        return 'UserName';
    }
    protected function attemptLogin(\Illuminate\Http\Request $request)
    {
        $user = \App\Models\User::where('UserName', $request->input('UserName'))
                            ->where('RowStatus', 1)
                            ->first();

        if ($user && $user->PasswordText === $request->input('password')) {
            \Auth::login($user, $request->filled('remember'));
            return true;
        }

        return false;
    }

}
