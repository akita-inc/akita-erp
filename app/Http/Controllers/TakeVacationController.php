<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MStaffs;
use App\Models\MWfAdditionalNotice;
use App\Models\MWfRequireApproval;
use App\Models\WApprovalStatus;
use App\Models\WPaidVacation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TakeVacationController extends Controller
{
    use ListTrait,FormTrait;
    public $table = "wf_paid_vacation";
    public $ruleValid = [
        'approval_kb' => 'required',
        'half_day_kb' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
        'days' => 'required|one_byte_number|length:11',
        'times' => 'required|one_byte_number|length:11',
        'reasons' => 'required|length:300',
    ];
    public $messagesCustom =[];
    public $labels=[
        'applicant_id' => '申請者',
        'applicant_office_nm' => '所属営業所',
        'approval_kb' => '休暇区分',
        'half_day_kb' => '時間区分',
        'start_date' => '開始日',
        'end_date' => '終了日',
        'days' => '日数',
        'times' => '時間数',
        'reasons' => '理由',
        'email_address' => '追加通知',
    ];
    public $currentData=null;
    public function __construct(){
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");

    }
    protected function getPaging()
    {
        return config('params.page_size_take_vacation');
    }

    protected function search($data){
        $where = array(
            'vacation_id' => $data['fieldSearch']['vacation_id'],
            'applicant_nm' => $data['fieldSearch']['applicant_nm'],
            'mst_business_office_id' => $data['fieldSearch']['sales_office'],
            'approval_kb' => $data['fieldSearch']['vacation_class'],
            'show_approved' =>$data['fieldSearch']['show_approved'],
            'show_deleted' =>$data['fieldSearch']['show_deleted'],
        );
        //select
        $this->query->select(
            'wf_paid_vacation.id',
            DB::raw("DATE_FORMAT(wf_paid_vacation.regist_date,'%Y/%m/%d') as applicant_date"),
            'ms.staff_cd as staff_cd',
            DB::raw('CONCAT_WS("    ",ms.last_nm,ms.first_nm) as applicant_nm'),
            DB::raw('CONCAT_WS("    ",ms.last_nm_kana,ms.first_nm_kana) as applicant_nm_kana'),
            'mbo.id as business_office_id',
            'mbo.business_office_nm as sales_office',
            'mgp.date_id as vacation_class_id',
            'mgp.date_nm as vacation_class_nm',
            'wf_paid_vacation.start_date as start_date',
            'wf_paid_vacation.days as days',
            'wf_paid_vacation.times as times',
            DB::raw("
            CASE	
                WHEN wf_paid_vacation.start_date = wf_paid_vacation.end_date THEN
                DATE_FORMAT(wf_paid_vacation.start_date,'%Y/%m/%d')
                ELSE CONCAT_WS( '～', DATE_FORMAT(wf_paid_vacation.start_date,'%Y/%m/%d'), DATE_FORMAT(wf_paid_vacation.end_date,'%Y/%m/%d')) 
            END AS period,
            CASE		
                WHEN ( wf_paid_vacation.days = 0 AND wf_paid_vacation.times = 0 ) THEN
                '' 
                WHEN ( wf_paid_vacation.days <> 0 AND wf_paid_vacation.times = 0 ) THEN
                CONCAT( wf_paid_vacation.days, '日' ) 
                WHEN ( wf_paid_vacation.days = 0 AND wf_paid_vacation.times <> 0 ) THEN
                CONCAT( wf_paid_vacation.times, '時間' )
                ELSE CONCAT( wf_paid_vacation.days, '日 ', wf_paid_vacation.times, '時間' ) 
            END AS datetime,
            CASE	
                WHEN (( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id) = 0) THEN
                '---'	
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 ) = 0 ) THEN
                '承認済み' 
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
		        '却下'
            ELSE 
                CONCAT(
                    COALESCE((
                    SELECT
                        was.title
                    FROM
                        wf_approval_status was			
                    WHERE
                        was.wf_type_id = 1 
                        AND was.wf_id = wf_paid_vacation.id
                        AND was.approval_fg = 0
                    ORDER BY
                        was.approval_steps,
                        was.id 
                        LIMIT 1 
                    ),''),
                    ' 承認待ち' 
                ) 
	        END AS approval_status,
	        CASE -- approval_fg
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_fg,

            CASE -- approval_levels
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_paid_vacation.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_levels
            "),
            'wf_paid_vacation.delete_at'
        );
        //join
        $this->query->join('mst_staffs as ms', function($join){
            $join->on('ms.staff_cd','=','wf_paid_vacation.applicant_id');
        });
        $this->query->join('mst_business_offices as mbo', function($join){
            $join->on('mbo.id','=','wf_paid_vacation.applicant_office_id');
        });
        $this->query->join('mst_general_purposes as mgp', function($join){
            $join->on('mgp.date_id','=','wf_paid_vacation.approval_kb')
                ->where('mgp.data_kb',config('params.data_kb')['vacation_indicator']);
        });
        //where
        if(Auth::user()->approval_levels === null){
            $this->query->where('wf_paid_vacation.applicant_id','=',Auth::user()->staff_cd);
        }
        if($where['vacation_id']!='')
        {
            $this->query->where('wf_paid_vacation.id','LIKE','%' .$where['vacation_id'].'%');
        }
        //
        if($where['applicant_nm']!='')
        {
            $this->query->where( DB::raw('CONCAT(ms.last_nm,ms.first_nm)'), 'LIKE', '%'.$where['applicant_nm'].'%');
        }
        //
        if($where['mst_business_office_id']!='')
        {
            $this->query->where('wf_paid_vacation.applicant_office_id',$where['mst_business_office_id']);
        }
        //
        if($where['approval_kb']!='')
        {
            $this->query->where('wf_paid_vacation.approval_kb',$where['approval_kb']);
        }
        //
        if($where['show_approved']!=true)
        {
            $this->query->whereRaw('(SELECT COUNT(*)
                                    FROM wf_approval_status
                                    WHERE wf_id = wf_paid_vacation.id 
                                    AND wf_type_id = 1 
                                    AND (approval_fg = 0 
                                    OR approval_fg  = 2)) > 0');
        }
        //
        if($where['show_deleted']!=true)
        {
            $this->query->whereNull('delete_at');
        }
        //Order by
        if ($data["order"]["col"] != '') {
            $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                if($orderCol  == 'days,times'){
                    $orderCol = 'days DESC,times DESC';
                }
                else if($orderCol  == 'approval_fg,approval_levels'){
                    $orderCol = 'approval_fg DESC,approval_levels DESC';
                }
                else{
                    $orderCol .= " DESC";
                }
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderBy('id','desc');
        }
    }

    public function index(Request $request){
        $fieldShowTable = [
            'id' => [
                "classTH" => "wd-100",
                "classTD" => "text-center",
                "sortBy"=>"id"
            ],
            'applicant_date' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"applicant_date"
            ],
            'applicant_nm' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"applicant_nm_kana"
            ],
            'sales_office' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"business_office_id"
            ],
            'vacation_class_nm' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"vacation_class_id"
            ],
            'period' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"start_date"
            ],
            'datetime' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"days,times"
            ],
            'approval_status' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"approval_fg,approval_levels"
            ],
        ];
        $mGeneralPurpose = new MGeneralPurposes();
        $mBussinessOffice = new MBusinessOffices();
        $businessOffices = $mBussinessOffice->getAllData();
        $vacationClasses = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['vacation_indicator']);
        return view('take_vacation.index',[
            'fieldShowTable'=>$fieldShowTable,
            'businessOffices' => $businessOffices,
            'vacationClasses' => $vacationClasses
        ]);
    }

    public function checkIsExist(Request $request, $id){
        $status= $request->get('status');
        $mode = $request->get('mode');
        $modified_at = $request->get('modified_at');
        $data = DB::table($this->table)->where('id',$id)/*->whereNull('delete_at')*/->first();
        if (isset($data)) {
            if($this->table!='empty_info' || ($mode!='edit' && $this->table=='empty_info') || ($mode=='edit' && $this->table=='empty_info' && Session::get('sysadmin_flg')==1)){
                if(!is_null($modified_at)){
                    if(Carbon::parse($modified_at) != Carbon::parse($data->modified_at)){
                        $message = Lang::get('messages.MSG04003');
                        return Response()->json(array('success'=>false, 'msg'=> $message));
                    }
                }
            }
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
        $mWPaidVacation = null;
        $mode = "register";
        $role = 1;
        $listWfAdditionalNotice = [];
        $listWApprovalStatus = [];
        $mWApprovalStatus = new WApprovalStatus();
        if($id != null){
            $modelWPaidVacation = new WPaidVacation();
            $mWPaidVacation = $modelWPaidVacation->getInfoByID($id);
            if(empty($mWPaidVacation)){
                abort('404');
            }else{
                $mWPaidVacation = $mWPaidVacation->toArray();
                $listWfAdditionalNotice = MWfAdditionalNotice::query()->select('staff_cd','email_address')->where('wf_id','=',$id)->where('wf_type_id','=',1)->get()->toArray();
                $listWApprovalStatus = $mWApprovalStatus->getListByWfID($id);
                $countVacationNotApproval = $mWApprovalStatus->countVacationNotApproval($id);
                $countVacationNotApprovalOfUserLogin = $mWApprovalStatus->countVacationNotApproval($id, true);
                $routeName = $request->route()->getName();
                switch ($routeName){
                    case 'take_vacation.approval':
                        $mode = 'approval';
                        if(!empty($mWPaidVacation['delete_at']) || is_null(Auth::user()->approval_levels) ||  $countVacationNotApprovalOfUserLogin<=0 || $mWPaidVacation['applicant_id']== Auth::user()->staff_cd  ){
                            $role = 2; // no authentication
                        }
                        break;
                    case 'take_vacation.reference':
                        $mode = 'reference';
                            if((!empty($mWPaidVacation['delete_at']) &&  $mWPaidVacation['applicant_id']!= Auth::user()->staff_cd) ||
                                (empty($mWPaidVacation['delete_at']) &&  is_null(Auth::user()->approval_levels ))
                            )
                                $role = 2;
                        break;
                    default:
                        $mode ='edit';
                        if(!empty($mWPaidVacation['delete_at'])  || $mWPaidVacation['applicant_id']!= Auth::user()->staff_cd || $countVacationNotApproval<=0 ){
                            $role = 2;
                        }
                }
            }
        }
        $mBusinessOffices = new MBusinessOffices();
        $mGeneralPurposes = new MGeneralPurposes();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices();
        $businessOfficeNm = $mBusinessOffices->select('business_office_nm')->where('id','=',Auth::user()->mst_business_office_id)->first();
        $listVacationIndicator= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.vacation_indicator'),'Empty');
        $listVacationAcquisitionTimeIndicator= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.vacation_acquisition_time_indicator'),'Empty');
        $currentDate = date('Y/m/d');
        return view('take_vacation.form', [
            'mWPaidVacation' => $mWPaidVacation,
            'businessOfficeNm' => $businessOfficeNm->business_office_nm,
            'listVacationIndicator' => $listVacationIndicator,
            'listVacationAcquisitionTimeIndicator' => $listVacationAcquisitionTimeIndicator,
            'currentDate' => $currentDate,
            'listBusinessOffices' => $listBusinessOffices,
            'role' => $role,
            'mode' => $mode,
            'fieldShowTable' => $fieldShowTable,
            'listWfAdditionalNotice' => json_encode($listWfAdditionalNotice,true),
            'listWApprovalStatus' => $listWApprovalStatus,
        ]);
    }

    public function beforeSubmit($data){
        if($data['half_day_kb']=='4'){
            $this->ruleValid['times'] = 'required|one_byte_number|between_custom:1,8|length:11';
        }

    }

    protected function validAfter( &$validator,$data ){
        $listWfAdditionalNotice = $data['wf_additional_notice'];
        $listMailChecked = [];
        $errorsEx = [];
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
            $query = $query->where(DB::raw("CONCAT(mst_staffs.last_nm , mst_staffs.first_nm ,mst_staffs.last_nm_kana ,mst_staffs.first_nm_kana)"),'LIKE','%'.$input['name'].'%');
        }
        if(isset($input['mst_business_office_id']) && !empty($input['mst_business_office_id'])){
            $query = $query->where('mst_staffs.mst_business_office_id','=',$input['mst_business_office_id']);
        }
        if ($input["order"]["col"] != '') {
            if ($input["order"]["col"] == 'staff_nm')
                $orderCol = 'CONCAT(mst_staffs.last_nm,mst_staffs.first_nm)';
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
            $query->orderBy('mst_staffs.last_nm_kana', 'mst_staffs.first_nm_kana');
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
                'msg'=> Lang::get('messages.MSG10010'),
            ]);
        }
    }

    protected function save($data){
        $id_before =  null;
        $mGeneralPurposes = new MGeneralPurposes();
        $listLevel= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.wf_level'),'Empty');
        $arrayInsert = $data;
        $listWfAdditionalNotice = $arrayInsert['wf_additional_notice'];
        $currentTime = date("Y-m-d H:i:s",time());
        $arrayInsert['regist_date'] = $currentTime;
        $mode = $arrayInsert["mode"];
        $approval_fg = $arrayInsert["approval_fg"];
        unset($arrayInsert["id"]);
        unset($arrayInsert["mode"]);
        unset($arrayInsert["staff_nm"]);
        unset($arrayInsert["applicant_office_nm"]);
        unset($arrayInsert["wf_additional_notice"]);
        unset($arrayInsert["approval_fg"]);
        $mStaff = new MStaffs();
        DB::beginTransaction();
        try{
            if(isset( $data["id"]) && $data["id"]){
                $id_before = $data["id"];
                $arrayInsert["modified_at"] = $currentTime;
                WPaidVacation::query()->where("id","=",$id_before)->update(['delete_at' => date("Y-m-d H:i:s",time())]);
                $configMail = config('params.vacation_edit_mail');
            }else{
                $configMail = config('params.vacation_register_mail');
            }
            $approval_levels_step_1 = "";
            $arrayInsert["create_at"] = $currentTime;
            $arrayInsert["modified_at"] = $currentTime;
            $id =  WPaidVacation::query()->insertGetId( $arrayInsert );
            if($id){
                $dataWfApprovalStatus = [];
                $fixValue = [
                    'wf_type_id' => 1,
                    'wf_id' => $id,
                    'approver_id' => null,
                    'approval_fg' => 0,
                    'approval_date' => null,
                    'send_back_reason' => null,
                ];
                $listRequireApproval = MWfRequireApproval::query()->where('wf_type','=',1)->where('applicant_section','=',Auth::user()->section_id)->get();
                if(count($listRequireApproval) > 0){
                    foreach ($listRequireApproval as $item){
                        if($item->approval_steps==1){
                            $approval_levels_step_1 = $item->approval_levels;
                        }
                        $row = $fixValue;
                        $row['approval_steps'] = $item->approval_steps;
                        $row['approval_levels'] = $item->approval_levels;
                        $row['approval_kb'] = $item->approval_kb;
                        $row['title'] = $listLevel[$item->approval_levels];
                        array_push($dataWfApprovalStatus, $row);
                    }
                    WApprovalStatus::query()->insert( $dataWfApprovalStatus );
                }

                $dataWfAdditionalNotice = [];
                foreach ($listWfAdditionalNotice as $key => $item){
                    if(!empty($item['email_address'])){
                        $row= $item;
                        $row['wf_type_id'] = 1;
                        $row['wf_id'] = $id;
                        array_push($dataWfAdditionalNotice, $row);
                    }else{
                        unset($listWfAdditionalNotice[$key]);
                    }
                }
                if(count($dataWfAdditionalNotice) > 0){
                    MWfAdditionalNotice::query()->insert( $dataWfAdditionalNotice );
                }
            }

            $mailTo =$mStaff->getListMailTo($arrayInsert['applicant_office_id'],$approval_levels_step_1);
            $mailCC = !empty(Auth::user()->mail) ? [Auth::user()->mail] : [];
            $mailCC = array_merge($mailCC,array_column($listWfAdditionalNotice,'email_address'));
            DB::commit();
            $this->handleMail($id,$configMail,$mailTo,$mailCC,$id_before);
            if(isset( $data["id"])){
                $this->backHistory();
                \Session::flash('message',Lang::get('messages.MSG04002'));
            }else{
                \Session::flash('message',Lang::get('messages.MSG03002'));
            }
        }catch (\Exception $e){
            DB::rollback();
            dd($e);
        }
        return $id;
    }

    public function handleMail($id,$configMail,$mailTo,$mailCC,$id_before){
        $mWPaidVacation = new WPaidVacation();
        $data = $mWPaidVacation->getInfoForMail($id);
        $field = ['[id]','[applicant_id]','[approval_kb]','[start_date]','[end_date]','[days]','[times]','[reasons]','[id_before]','[title]'];
        $data['id_before'] = $id_before;
        $data['title'] = null;
        $text = str_replace($field, [$data['id'],$data['applicant_id'],$data['approval_kb'],$data['start_date'],$data['end_date'],$data['days'],$data['times'],$data['reasons'],$data['id_before'],$data['title']],
            $configMail['template']);
        $subject = str_replace(['[id]','[approval_kb]','[applicant_id]','[applicant_office_id]'],[$data['id'],$data['approval_kb'],$data['applicant_id'],$data['applicant_office_id']],$configMail["subject"]);
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
        if (WPaidVacation::query()->where("id","=",$id)->update(['delete_at' => date("Y-m-d H:i:s",time())])) {
            \Session::flash('message',Lang::get('messages.MSG10004'));
            $response = ['data' => 'success'];
        } else {
            \Session::flash('message',Lang::get('messages.MSG06002'));
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

}
