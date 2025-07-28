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

        if (!$user || $user->PasswordText !== $request->input('password')) {
            return false;
        }

        // STEP 2: ตรวจสอบว่ามี UserName นี้ใน SQL Server (DB B) หรือไม่
        $userInSqlsrv = \DB::connection('sqlsrv_secondary')
            ->table('users')  // ชื่อตารางใน SQL Server
            ->where('UserName', $request->input('UserName'))
            ->first();

        if (!$userInSqlsrv) {
            return false; // ไม่ให้ login ถ้าไม่มี user นี้ใน DB B
        }

        // STEP 3: Login สำเร็จ
        \Auth::login($user, $request->filled('remember'));

        $permissions = \DB::connection('sqlsrv_secondary')
            ->table('sys_role_menu_permissions')
            ->join('sys_menus', 'sys_menus.MenuID', '=', 'sys_role_menu_permissions.MenuID')
            ->where('sys_role_menu_permissions.RoleID', $userInSqlsrv->RoleID)
            ->where('CanView', 1)
            ->select('sys_menus.MenuName', 'sys_menus.Route')
            ->get();
        session(['menu_permissions' => $permissions]);
        return true;
    }
}

