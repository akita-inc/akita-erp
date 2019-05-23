<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function convertKana(Request $request){
        $data = $request->get('data');
        $string = Common::convertToKana($data,'katakana');
        return Response()->json(array('success'=>true,'info'=>$string));
    }
}
