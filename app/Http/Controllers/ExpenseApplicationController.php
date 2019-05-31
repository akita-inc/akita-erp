<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSaleses;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
class ExpenseApplicationController extends Controller
{
    use ListTrait;
    public $table = "wf_business_entertaining";
    public $ruleValid = [
    ];
    public $messagesCustom =[];
    public $labels=[];
    public $csvColumn=[
    ];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

    }
    protected function search($data){

    }

    public function index(Request $request){
        $fieldShowTable = [
        ];

        return view('sales_lists.index',[

        ]);
    }


}
