<?php

namespace App\Http\Controllers;

use App\Models\MBusinessOffices;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $mBusinessOffices = new MBusinessOffices();
        $businessOfficeNm = $mBusinessOffices->select('id','business_office_nm')->where('id','=',Auth::user()->mst_business_office_id)->first();
        return view('welcome',[
            'businessOfficeNm' => $businessOfficeNm ? $businessOfficeNm->business_office_nm: null,
        ]);
    }
}
