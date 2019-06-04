<?php

namespace App\Http\Controllers;

use App\Models\MBusinessOffices;
use App\Models\MStaffAuths;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('staffs_accessible_kb', 1);
            Session::put('suppliers_accessible_kb', 1);
            Session::put('customers_accessible_kb', 1);
            Session::put('vehicles_accessible_kb', 1);
//            Session::put('staffs_accessible_kb', 9);
//            Session::put('suppliers_accessible_kb', 9);
//            Session::put('customers_accessible_kb', 9);
//            Session::put('vehicles_accessible_kb', 9);
//            $auths = new MStaffAuths();
//            $auths = $auths->getAccessibleKbByCondition(['mst_staff_id' => Auth::user()->id]);
//            if (count($auths) > 0) {
//                foreach ($auths as $auth) {
//                    if ($auth['screen_category_id'] == 1)
//                        Session::put('staffs_accessible_kb', $auth['accessible_kb']);
//                    elseif ($auth['screen_category_id'] == 2)
//                        Session::put('suppliers_accessible_kb', $auth['accessible_kb']);
//                    elseif ($auth['screen_category_id'] == 3)
//                        Session::put('customers_accessible_kb', $auth['accessible_kb']);
//                    elseif ($auth['screen_category_id'] == 4)
//                        Session::put('vehicles_accessible_kb', $auth['accessible_kb']);
//                }
//            }
            $mBusinessOffices = new MBusinessOffices();
            $businessOfficeNm = $mBusinessOffices->select('id','business_office_nm')->where('id','=',Auth::user()->mst_business_office_id)->first();
            View::share('businessOfficeNm',$businessOfficeNm ? $businessOfficeNm->business_office_nm: null);
            return $next($request);
        });
    }
}
