<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class StaffUpdateMiddleware
{
    public function handle($request, Closure $next)
    {
        if(Auth::check())
        {
            $staff = DB::table("mst_staffs")
                ->where('adhibition_start_dt', '<=', date('Y-m-d'))
                ->where('adhibition_end_dt', '>=', date('Y-m-d'))
                ->where("id","=",Auth::user()->id)
                ->whereNull("deleted_at")
                ->first();

            if( empty($staff) || $staff->password != $request->session()->get("password_old") ){
                if($request->ajax()){
                    abort(403);
                }
                return redirect('/logoutError');
            }
            return $next($request);
        }
    }
}
