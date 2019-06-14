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
    public function getLogin(Request $request)
    {
        if(Auth::check())
        {
            return redirect('/');
        }
        else
        {
            if(!empty($request->session()->get("password_old"))){
                return redirect('/logoutError');
            }
            return view('auth.login');
        }
    }
    public  function changePassword(Request $request)
    {
        $data=$request->all();
        $rules = [
            'password' => 'required|min:6'
        ];
        $labels = Lang::get("common.modal.change_password");
        $validator = Validator::make( $data, $rules ,[],$labels );
        if ($validator->fails()) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }
        else
        {
            $staff = MStaffs::where('staff_cd', Auth::user()->staff_cd)->first();
            if($staff)
            {
                $staff['password']=bcrypt($data['password']);
                $staff->save();
                Session::put('password_old',  $staff['password']);
                Session::put('sysadmin_flg', $staff->sysadmin_flg);
                Session::flash('passwordSuccessMsg',Lang::get('messages.MSG04002'));
            }
        }
        return response()->json(['success' => TRUE, 'message' => Lang::get('messages.MSG04002')]);
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
            $staff = MStaffs::where('staff_cd', $data['staff_cd'])->first();
            if (Auth::attempt(['staff_cd' => $data['staff_cd'], 'password' => $data['password']], $remember)) {
                Session::put('password_old', $staff->password);
                Session::put('sysadmin_flg', $staff->sysadmin_flg);
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
