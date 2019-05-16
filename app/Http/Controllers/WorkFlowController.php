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
class WorkFlowController extends Controller
{
    use ListTrait;
    public $table = "wf_type";
    public $ruleValid = [

    ];
    public $messagesCustom =[];
    public $labels=[];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

    }

    protected function search($data){
        $where = array(
            'name' => $data['fieldSearch']['name'],
        );
        $this->query->select('id','name','steps');
        if($where['name']!='')
        {
            $this->query->where('name','LIKE','%'.$where['name'].'%');
        }
        $this->query->whereNull('delete_at');
        if ($data["order"]["col"] != '') {
            $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderBy('id','asc');

        }
    }

    public function index(Request $request){
        $fieldShowTable = [
            'id' => [
                "classTH" => "wd-100",
                "classTD" => "text-center",
                "sortBy"=>"id"
            ],
            'name' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"name"
            ],
            'steps' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ]
        ];
        return view('work_flow.index',[
            'fieldShowTable'=>$fieldShowTable
        ]);
    }

    public function store(Request $request, $id=null){
        return view('work_flow.form', [

        ]);
    }


}
