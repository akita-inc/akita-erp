<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:11 PM
 */
namespace App\Http\Controllers;

//use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class StaffsController extends Controller
{
//    use ListTrait;
    public $table = "mst_staffs";

    protected function search($data){


    }

    public function index(Request $request){
        $fieldShowTable = [
            'mst_customers_cd' => [
                "classTH" => "wd-100"
            ],

        ];
        return view('staffs.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function create(Request $request){

        return view('staffs.create');
    }
}
