<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSaleses;
use App\Models\MWfRequireApproval;
use App\Models\MWfRequireApprovalBase;
use App\Models\MWfType;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
class WorkFlowController extends Controller
{
    use ListTrait, FormTrait;
    public $table = "wf_type";
    public $ruleValid = [
        "name" => "required",
        "steps" => "required|between_custom:1,10",
    ];
    public $messagesCustom =[
        'name.required' => '名称を入力してください。',
        'steps.required' => '10以下の数値を入力してください。',
        'between_custom' => '10以下の数値を入力してください',
    ];
    public $labels=[
        "name" => "名称",
        "steps" => "承認段階数",
    ];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

    }
    protected function getPaging()
    {
        return config('params.page_size_work_flow');
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
    public function checkIsExist(Request $request, $id){
        $status= $request->get('status');
        $mode = $request->get('mode');
        $modified_at = $request->get('modified_at');
        $data = DB::table($this->table)->where('id',$id)->whereNull('delete_at')->first();
        if (isset($data)) {
            if($this->table!='empty_info' || ($mode!='edit' && $this->table=='empty_info') || ($mode=='edit' && $this->table=='empty_info' && Session::get('sysadmin_flg')==1)){
                if(!is_null($modified_at)){
                    if(Carbon::parse($modified_at) != Carbon::parse($data->modified_at)){
                        $message = Lang::get('messages.MSG04003');
                        return Response()->json(array('success'=>false, 'msg'=> $message));
                    }
                }
            }
            return Response()->json(array('success'=>true));
        } else {
            if($this->table=='empty_info'){
                if($mode=='edit' || $mode=='delete'){
                    $message = Lang::get('messages.MSG04004');
                }else{
                    switch ($status){
                        case 1:
                            $message = Lang::get('messages.MSG10021');
                            break;
                        case 2:
                            $message = Lang::get('messages.MSG10015');
                            break;
                        case 8:
                            $message = Lang::get('messages.MSG10018');
                            break;
                    }
                }
                return Response()->json(array('success'=>false, 'msg'=> $message));
            }else{
                return Response()->json(array('success'=>false, 'msg'=> is_null($mode) ? Lang::trans('messages.MSG04003') : Lang::trans('messages.MSG04004')));
            }
        }
    }

    public function store(Request $request, $id=null){
        $mWfType = null;
        $mWfRequireApprovalBase = null;
        $mWfRequireApproval = null;
        $mGeneralPurposes = new MGeneralPurposes();
        $listWfLevel =  $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.wf_level'),'Empty');
        $listWfApprovalIndicator =  $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.wf_approval_indicator'),'Empty');

        if($id != null) {
            $mWfType = MWfType::find($id);
            if(empty($mWfType)){
                abort('404');
            }else {
                $mWfType = $mWfType->toArray();
            }
        }
        return view('work_flow.form', [
            'mWfType' => $mWfType,
            'mWfRequireApprovalBase' => $mWfRequireApprovalBase,
            'mWfRequireApproval' => $mWfRequireApproval,
            'listWfLevel' => $listWfLevel,
            'listWfApprovalIndicator' => $listWfApprovalIndicator,
        ]);
    }

    public function getListWfApplicantAffiliationClassification(Request $request){
        $mGeneralPurposes = new MGeneralPurposes();
        $listWfApplicantAffiliationClassification= $mGeneralPurposes->getInfoByDataKB(config('params.data_kb.wf_applicant_affiliation_classification'));
        return response()->json([
            'info'=> $listWfApplicantAffiliationClassification,
        ]);
    }

    public function validateData(Request $request){
        $data=  $request->all();
        $validator = Validator::make($data, $this->ruleValid,$this->messagesCustom,$this->labels);
        if ($validator->fails()) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else{
            return response()->json([
                'success'=>true,
            ]);
        }
    }

    protected function save($data){
        $arrayInsert = $data;
        $currentTime = date("Y-m-d H:i:s",time());
        $mWfType = new MWfType();
        $mWfRequireApprovalBase = new MWfRequireApprovalBase();
//        dd($data);
        $id = isset($data['id']) ?$data['id']  :null;
        if(!is_null($id)){
            $mWfType = $mWfType->find($id);
        }
        DB::beginTransaction();
        try{
            $mWfType->name = $data['name'];
            $mWfType->steps = $data['steps'];
            $mWfType->save();
            $dataApprovalBase = [];
            foreach ($data['mst_wf_require_approval_base'] as $item){
                $row = $item;
                $row['wf_type'] = $mWfType->id;
                $row['create_at'] = $currentTime;
                array_push($dataApprovalBase,$row);
            }
            MWfRequireApprovalBase::query()->insert($dataApprovalBase);
            $dataApproval = [];
            foreach ($data['mst_wf_require_approval'] as $section =>$items){
                foreach ($items['list'] as $item) {
                    $row = $item;
                    $row['wf_type'] = $mWfType->id;
                    $row['applicant_section'] = $section;
                    $row['create_at'] = $currentTime;
                    array_push($dataApproval, $row);
                }
            }
            MWfRequireApproval::query()->insert($dataApproval);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            dd($e);
        }
        return response()->json([
            'success'=>true,
            'message'=> [],
        ]);
    }

    public function getListApprovalBase(Request $request){
        $wf_type = $request->input('wf_type');
        $listApprovalBase = MWfRequireApprovalBase::query()->where('wf_type', '=', $wf_type)->get();
        return response()->json([
            'info'=>$listApprovalBase,
        ]);
    }

    public function getListApproval(Request $request){
        $wf_type = $request->input('wf_type');
        $listApprovalBase = MWfRequireApproval::query()->where('wf_type', '=', $wf_type)->get()->groupBy('applicant_section')->toArray();
        return response()->json([
            'info'=>$listApprovalBase,
        ]);
    }
}
