<?php

namespace App\Http\Controllers\TraitRepositories;


use App\Models\MBusinessOffices;
use App\Models\MStaffs;
use App\Models\MWfAdditionalNotice;
use App\Models\MWfRequireApproval;
use App\Models\WApprovalStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

trait WorkflowTrait
{
    public function validateWfAdditionalNotice(&$validator,$listWfAdditionalNotice){
        $errorsEx = [];
        $listMailChecked = [];
        foreach ($listWfAdditionalNotice as $key => $item){
            $validatorEx = Validator::make(['email_address' => $item['email_address']], ['email_address' => 'length:255|nullable|email_format|email_character'], $this->messagesCustom, $this->labels);
            if ($validatorEx->fails()) {
                $errorsEx[$key] = $validatorEx->errors()->first();
            }else{
                if(!empty($item['email_address'])){
                    if(strstr($item['email_address'],"@") != config('params.domain_email_address')){
                        $errorsEx[$key] = Lang::get('messages.MSG10026');
                    }else{
                        if(!in_array($item['email_address'],$listMailChecked)){
                            array_push($listMailChecked,$item['email_address']);
                        }else{
                            $errorsEx[$key] = Lang::get('messages.MSG10027');
                        }
                    }
                }
            }
        }
        if (count($errorsEx) > 0) {
            $validator->errors()
                ->add("wf_additional_notice", $errorsEx);
        }
    }

    public function searchStaff(Request $request){
        $input = $request->all();
        $mStaff = new MStaffs();
        $query = $mStaff
            ->select(
                'mst_staffs.staff_cd',
                DB::raw("CONCAT(mst_staffs.last_nm,mst_staffs.first_nm) as staff_nm"),
                'mst_business_offices.business_office_nm',
                'mst_staffs.mail'
            )
            ->join(DB::raw('mst_business_offices'), function ($join) {
                $join->on('mst_business_offices.id', '=', 'mst_staffs.mst_business_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->whereNotNull('mst_staffs.mail')
            ->whereNull('mst_staffs.deleted_at');
        if(isset($input['name']) && !empty($input['name'])){
            $query = $query->where(DB::raw("CONCAT_WS('',mst_staffs.last_nm , mst_staffs.first_nm ,mst_staffs.last_nm_kana ,mst_staffs.first_nm_kana)"),'LIKE','%'.$input['name'].'%');
        }
        if(isset($input['mst_business_office_id']) && !empty($input['mst_business_office_id'])){
            $query = $query->where('mst_staffs.mst_business_office_id','=',$input['mst_business_office_id']);
        }
        if ($input["order"]["col"] != '') {
            if ($input["order"]["col"] == 'staff_nm')
                $orderCol = "CONCAT_WS('',mst_staffs.last_nm_kana,mst_staffs.first_nm_kana)";
            else if($input["order"]["col"]=='business_office_nm')
                $orderCol='mst_business_offices.business_office_nm';
            else if($input["order"]["col"]=='mail')
                $orderCol='mst_staffs.mail';
            else
                $orderCol = $input["order"]["col"];
            if (isset($input["order"]["descFlg"]) && $input["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $query->orderbyRaw($orderCol);
        } else {
            $query->orderByRaw("CONCAT_WS('',mst_staffs.last_nm_kana,mst_staffs.first_nm_kana) ASC");
        }
        $data = $query->get();
        if(count($data) > 0){
            return response()->json([
                'success'=>true,
                'info'=> $data,
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=> Lang::get('messages.MSG05001'),
            ]);
        }
    }

    public function registerWApprovalStatus($wf_id,$listLevel,&$approval_levels_step_1){
        $dataWfApprovalStatus = [];
        $fixValue = [
            'wf_type_id' => $this->wf_type_id,
            'wf_id' => $wf_id,
            'approver_id' => null,
            'approval_fg' => 0,
            'approval_date' => null,
            'send_back_reason' => null,
        ];
        $listRequireApproval = MWfRequireApproval::query()->where('wf_type', '=', $this->wf_type_id)->where('applicant_section', '=', Auth::user()->section_id)->get();
        if (count($listRequireApproval) > 0) {
            foreach ($listRequireApproval as $item) {
                if ($item->approval_steps == 1) {
                    $approval_levels_step_1 = $item->approval_levels;
                }
                $row = $fixValue;
                $row['approval_steps'] = $item->approval_steps;
                $row['approval_levels'] = $item->approval_levels;
                $row['approval_kb'] = $item->approval_kb;
                $row['title'] = $listLevel[$item->approval_levels];
                array_push($dataWfApprovalStatus, $row);
            }
            WApprovalStatus::query()->insert($dataWfApprovalStatus);
        }
    }

    public function registerWfAdditionalNotice($wf_id,$listWfAdditionalNotice){
        $dataWfAdditionalNotice = [];
        foreach ($listWfAdditionalNotice as $key => $item) {
            if (!empty($item['email_address'])) {
                $row =[];
                $row['staff_cd'] = $item['staff_cd'];
                $row['email_address'] = $item['email_address'];
                $row['wf_type_id'] = $this->wf_type_id;
                $row['wf_id'] = $wf_id;
                array_push($dataWfAdditionalNotice, $row);
            } else {
                unset($listWfAdditionalNotice[$key]);
            }
        }
        if (count($dataWfAdditionalNotice) > 0) {
            MWfAdditionalNotice::query()->insert($dataWfAdditionalNotice);
        }
    }

    public function getListMailRegisterOrEdit($arrayInsert,$approval_levels_step_1,$listWfAdditionalNotice,&$mailTo, &$mailCC){
        $mStaff = new MStaffs();
        $mailTo = $mStaff->getListMailTo($arrayInsert['applicant_office_id'], $approval_levels_step_1, $arrayInsert['applicant_id']);
        if (count($mailTo) == 0) {
            $mailCC = !empty(Auth::user()->mail) ? [Auth::user()->mail] : [];
            $mailTo = array_column($listWfAdditionalNotice, 'email_address');
        } else {
            $mailCC = !empty(Auth::user()->mail) ? [Auth::user()->mail] : [];
            $mailCC = array_merge($mailCC, array_column($listWfAdditionalNotice, 'email_address'));
        }
    }

    public function handleApproval($wf_id,$listWfAdditionalNotice,$arrayInsert,$applicant_id,$applicant_mail,&$mailTo, &$mailCC){
        $mWApprovalStatus = new WApprovalStatus();
        $mStaff = new MStaffs();
        $dataWfAdditionalNotice = [];
        foreach ($listWfAdditionalNotice as $key => $item) {
            if (!empty($item['email_address'])) {
                if (!isset($item['id'])) {
                    $row = $item;
                    $row['wf_type_id'] = $this->wf_type_id;
                    $row['wf_id'] = $wf_id;
                    array_push($dataWfAdditionalNotice, $row);
                }
            } else {
                unset($listWfAdditionalNotice[$key]);
            }
        }
        if (count($dataWfAdditionalNotice) > 0) {
            MWfAdditionalNotice::query()->insert($dataWfAdditionalNotice);
        }
        $minStepLevel = $mWApprovalStatus->getMinStepsLevel($wf_id, $this->wf_type_id);
        if($minStepLevel){
            $mailTo =$mStaff->getListMailTo($arrayInsert['applicant_office_id'],$minStepLevel->approval_levels,$applicant_id);
        }else{
            $mailTo = !empty($applicant_mail) ? [$applicant_mail] : [];
            $mailTo = array_merge($mailTo,array_filter(array_column($listWfAdditionalNotice, 'email_address')));
        }
    }

    public function handleReject($wf_id,$listWfAdditionalNotice,$arrayInsert,$applicant_id,$applicant_mail,&$mailTo, &$mailCC){
        $mWApprovalStatus = new WApprovalStatus();
        $mStaff = new MStaffs();
        $listWApprovalStatus = $mWApprovalStatus->getListByWfID($wf_id,$this->wf_type_id);
        $mailTo = !empty($applicant_mail) ? [$applicant_mail] : [];
        foreach ($listWApprovalStatus as $item){
            $listMail = $mStaff->getListMailTo($arrayInsert['applicant_office_id'],$item->approval_levels,$applicant_id);
            $mailTo = array_merge($mailTo,$listMail);
        }
        $mailTo = array_merge($mailTo,array_filter(array_column($listWfAdditionalNotice, 'email_address')));
    }

    public function sendMail($configMail,$mailTo,$mailCC,$subject,$text){
        if(count($mailTo) > 0){
            Mail::raw($text,
                function ($message) use ($configMail,$subject,$mailTo,$mailCC) {
                    $message->from($configMail["from"]);
                    if(count($mailCC) > 0){
                        $message->cc($mailCC);
                    }
                    $message->to($mailTo)
                        ->subject($subject);
                });
        }
    }

    public function delete($id)
    {
        $this->backHistory();
        if (DB::table($this->table)->where("id","=",$id)->update(['delete_at' => date("Y-m-d H:i:s",time())])) {
            \Session::flash('message',Lang::get('messages.MSG10004'));
            $response = ['data' => 'success'];
        } else {
            \Session::flash('message',Lang::get('messages.MSG06002'));
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExistWf($request, $id){
        $approval_fg= $request->get('approval_fg');
        $mode = $request->get('mode');
        $modified_at = $request->get('modified_at');
        $data = DB::table($this->table)->where('id',$id)->first();

        if(is_null($mode)){
            //1. 承認ステータスを判断
            // 1-1. 未承認の承認ステータスが1レコード以上ある場合
            $WApprovalStatus = new WApprovalStatus();
            $approvalStatus = $WApprovalStatus::where(['wf_id'=>$data->id,'approval_fg'=>0])->get();
            $return =['mode' =>''];
            if($data->delete_at != null){
                $return['mode'] = 'reference';
            }
            else{
                if($approvalStatus->count() >= 1){
                    if($data->applicant_id == Auth::user()->staff_cd){//1-1-1. 申請者＝ログインID
                        $return['mode'] = 'edit';
                    }
                    else{//1-1-2.  申請者 != ログインID
                        if($WApprovalStatus::where(['wf_id'=>$data->id,'approval_fg'=>0,'approval_levels'=>Auth::user()->approval_levels])->count() > 0){// 1-1-2-1. ログイン者＝承認権限を持っている（mst_staffs.approval_levels is not null）かつ、その承認レベルが未承認である。
                            $return['mode'] = 'approval';
                        }else{//1-1-2-2. それ以外
                            $return['mode'] = 'reference';
                        }
                    }
                }else{//1-2. それ以外（すべて承認済みor却下済み）
                    $return['mode'] = 'reference';
                }
            }
            return Response()->json(array_merge(array('success'=>true),$return));
        }else{
            if (is_null($data->delete_at)) {
                if($this->table!='empty_info' || ($mode!='edit' && $this->table=='empty_info') || ($mode=='edit' && $this->table=='empty_info' && Session::get('sysadmin_flg')==1)){

                    if(!is_null($modified_at)){
                        if(Carbon::parse($modified_at) != Carbon::parse($data->modified_at)){
                            $message = Lang::get('messages.MSG04003');
                            return Response()->json(array('success'=>false, 'msg'=> $message));
                        }
                    }
                }

                $WApprovalStatus = new WApprovalStatus();
                $approvalStatus = $WApprovalStatus::where(['wf_id'=>$data->id,'approval_fg'=>0])->get();
                if($approvalStatus->count() <= 0){
                    return Response()->json(array('success'=>false, 'msg'=> Lang::get('messages.MSG04003')));
                }
                return Response()->json(array('success'=>true));
            } else {
                if($mode=='edit' || $mode=='delete'){
                    $message = Lang::get('messages.MSG04004');
                }else{
                    if ($approval_fg){
                        $message = Lang::get('messages.MSG10018');
                    }else{
                        $message = Lang::get('messages.MSG10021');
                    }
                }
                return Response()->json(array('success'=>false, 'msg'=> $message));
            }
        }
    }

    public function checkAuthentication($id,$modelInfo,$request, &$mode,&$role){
        $mWApprovalStatus = new WApprovalStatus();
        $countVacationNotApproval = $mWApprovalStatus->countVacationNotApproval($id, $this->wf_type_id);
        $countVacationNotApprovalOfUserLogin = $mWApprovalStatus->countVacationNotApproval($id,$this->wf_type_id, true);
        $routeName = $request->route()->getName();
        switch (true){
            case strpos($routeName, 'approval') !== false:
                $mode = 'approval';
                if(!empty($modelInfo['delete_at']) || is_null(Auth::user()->approval_levels) ||  $countVacationNotApprovalOfUserLogin<=0 || $modelInfo['applicant_id']== Auth::user()->staff_cd  ){
                    $role = 2; // no authentication
                }
                break;
            case strpos($routeName, 'reference') !== false:
                $mode = 'reference';
                if((!empty($modelInfo['delete_at']) &&  $modelInfo['applicant_id']!= Auth::user()->staff_cd &&  is_null(Auth::user()->approval_levels )) ||
                    (empty($modelInfo['delete_at']) &&  is_null(Auth::user()->approval_levels ))
                )
                    $role = 2;
                break;
            default:
                $mode ='edit';
                if(!empty($modelInfo['delete_at'])  || $modelInfo['applicant_id']!= Auth::user()->staff_cd || $countVacationNotApproval<=0 ){
                    $role = 2;
                }
        }
    }

    public function beforeStore($id){
        $listWfAdditionalNotice = [];
        $listWApprovalStatus = [];
        $fieldShowTable = [
            'staff_nm' => [
                "classTH" => "wd-100",
                "sortBy"=>"staff_nm"
            ],
            'business_office_nm'=> [
                "classTH" => "wd-60",
                "sortBy"=>"business_office_nm"
            ],
            'mail'=> [
                "classTH" => "wd-120",
                "sortBy"=>"mail"
            ],
        ];
        $mBusinessOffices = new MBusinessOffices();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices(trans('common.kara_select_option'));
        $businessOfficeNm = $mBusinessOffices->select('id','business_office_nm')->where('id','=',Auth::user()->mst_business_office_id)->first();

        if($id != null) {
            $mWApprovalStatus = new WApprovalStatus();
            $listWfAdditionalNotice = MWfAdditionalNotice::query()->select('id', 'staff_cd', 'email_address')->where('wf_id', '=', $id)->where('wf_type_id', '=', $this->wf_type_id)->get()->toArray();
            $listWApprovalStatus = $mWApprovalStatus->getListByWfID($id, $this->wf_type_id);
        }
        return [
            'fieldShowTable' => $fieldShowTable,
            'businessOfficeNm' => $businessOfficeNm ? $businessOfficeNm->business_office_nm: null,
            'businessOfficeID' => $businessOfficeNm ? $businessOfficeNm->id: null,
            'listBusinessOffices' => $listBusinessOffices,
            'listWfAdditionalNotice' => json_encode($listWfAdditionalNotice,true),
            'listWApprovalStatus' => $listWApprovalStatus,
        ];
    }
}