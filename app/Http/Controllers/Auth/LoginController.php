<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\MStaffs;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\MessageBag;
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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest')->except('logout');
    }
    public function getLogin()
    {
        if(Auth::check())
        {
            return redirect('/');
        }
        else
        {
            return view('auth.login');
        }
    }

    public function postLogin(Request $request)
    {
        $data=$request->all();
        $rules = [
            'staff_cd' =>'required',
            'password' => 'required'
        ];
        $validator = Validator::make($data, $rules);
        $remember=isset($data['remember'])?true:false;
        if ($validator->fails()) {
            $errors = new MessageBag(['errorlogin' => trans('messages.MSG01001')]);
            return redirect()->back()->withInput($request->only('staff_cd', 'remember'))->withErrors($errors);
        } else {
            $staff = MStaffs::where('staff_cd', $data['staff_cd'])
                ->where('adhibition_start_dt', '<=', date('Y-m-d'))
                ->where('adhibition_end_dt', '>=', date('Y-m-d'))
                ->first();
            if ( isset($staff) && Auth::attempt(['id' => $staff->id, 'password' => $data['password']], $remember)) {
                Session::put('password_old', $staff->password);
                return redirect('/');
            } else {
                $errors = new MessageBag(['errorlogin' => trans('messages.MSG01003')]);
                return redirect()->back()->withInput($request->only('staff_cd', 'remember'))->withErrors($errors);
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::put('password_old',null);
        return redirect('/login');
    }
    public function logoutError(Request $request)
    {
        Auth::logout();
        Session::put('password_old',null);
        Session::flash('message',Lang::get('messages.MSG10008'));
        return redirect('/login');
    }
}
