<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Http\Controllers\TraitRepositories\WorkflowTrait;
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
    use ListTrait,FormTrait,WorkflowTrait;
    public $table = "wf_paid_vacation";
    public $ruleValid = [
        'applicant_office_nm' => 'required',
        'approval_kb' => 'required',
        'half_day_kb' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
        'days' => 'required|one_byte_number|number_range_custom:127|length:11',
        'times' => 'required|one_byte_number|length:11',
        'reasons' => 'required|length:200',
    ];
    public $messagesCustom =[];
    public $labels=[
        'applicant_id' => '申請者',
        'applicant_office_nm' => '所属営業所',
        'approval_kb' => '休暇区分',
        'half_day_kb' => '時間区分',
        'date' => '期間',
        'start_date' => '開始日',
        'end_date' => '終了日',
        'days' => '日数',
        'times' => '時間数',
        'reasons' => '理由',
        'email_address' => '追加通知',
        'send_back_reason' => '却下理由',
    ];
    public $currentData=null;
    public $wf_type_id="";
    public function __construct(){
        parent::__construct();
        date_default_timezone_set("Asia/Tokyo");
        $this->wf_type_id = config('params.vacation_wf_type_id_default');

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
                        was.wf_type_id = ".$this->wf_type_id." 
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
                                    AND wf_type_id = '.$this->wf_type_id.' 
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
        return $this->checkIsExistWf($request,$id);
    }

    public function store(Request $request, $id=null){
        $mWPaidVacation = null;
        $mode = "register";
        $role = 1;
        $mWApprovalStatus = new WApprovalStatus();
        if($id != null){
            $mWPaidVacation = new WPaidVacation();
            $mWPaidVacation = $mWPaidVacation->getInfoByID($id);
            if(empty($mWPaidVacation)){
                abort('404');
            }else{
                $mWPaidVacation = $mWPaidVacation->toArray();
                $this->checkAuthentication($id,$mWPaidVacation,$request, $mode,$role);
            }
        }
        $arrayStore = $this->beforeStore($id);
        $mGeneralPurposes = new MGeneralPurposes();
        $listVacationIndicator= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.vacation_indicator'),'Empty');
        $listVacationAcquisitionTimeIndicator= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.vacation_acquisition_time_indicator'),'Empty');
        $currentDate = date('Y/m/d');
        return view('take_vacation.form', array_merge($arrayStore,[
            'mWPaidVacation' => $mWPaidVacation,
            'listVacationIndicator' => $listVacationIndicator,
            'listVacationAcquisitionTimeIndicator' => $listVacationAcquisitionTimeIndicator,
            'currentDate' => $currentDate,
            'role' => $role,
            'mode' => $mode,
        ]));
    }

    public function beforeSubmit($data){

        if($data['half_day_kb']=='4'){
            $this->ruleValid['times'] = 'required|one_byte_number|between_custom:1,8|length:11';
        }
        if($data['mode']=='approval' && !is_null($data['approval_fg']) && $data['approval_fg']==0){
            $this->ruleValid['send_back_reason'] = 'required|length:200';
        }
    }

    protected function validAfter( &$validator,$data ){
        $listWfAdditionalNotice = $data['wf_additional_notice'];
        if ($data['start_date'] != "" && $data['end_date'] != ""
            && Carbon::parse($data['start_date']) > Carbon::parse($data['end_date'])) {
            $validator->errors()->add('start_date', str_replace(' :attribute', $this->labels['date'], Lang::get('messages.MSG02014')));
        }
        $this->validateWfAdditionalNotice($validator,$listWfAdditionalNotice);
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
        $send_back_reason  = $arrayInsert["send_back_reason"];
        unset($arrayInsert["id"]);
        unset($arrayInsert["mode"]);
        unset($arrayInsert["staff_nm"]);
        unset($arrayInsert["applicant_office_nm"]);
        unset($arrayInsert["wf_additional_notice"]);
        unset($arrayInsert["approval_fg"]);
        unset($arrayInsert["send_back_reason"]);
        $mStaff = new MStaffs();
        $mWApprovalStatus = new WApprovalStatus();
        $mailCC = [];
        $mailTo = [];
        DB::beginTransaction();
        try{
            if(isset( $data["id"]) && $data["id"]){
                $arrayInsert["modified_at"] = $currentTime;
                if($mode=='edit'){
                    $id_before = $data["id"];
                    WPaidVacation::query()->where("id","=",$id_before)->update(['delete_at' => date("Y-m-d H:i:s",time())]);
                    $configMail = config('params.vacation_edit_mail');
                }else{
                    if($approval_fg==1){
                        $mWApprovalStatus->approvalVacation($data["id"], $this->wf_type_id, $currentTime);
                        $configMail = config('params.vacation_approval_mail');
                    }
                    if($approval_fg==0){
                        $mWApprovalStatus->rejectVacation($data["id"],  $this->wf_type_id,$currentTime,$data['send_back_reason']);
                        $configMail = config('params.vacation_reject_mail');
                    }
                }

            }else{
                $configMail = config('params.vacation_register_mail');
            }
            if($mode=='register' || $mode=='edit') {
                $approval_levels_step_1 = "";
                $arrayInsert["create_at"] = $currentTime;
                $arrayInsert["modified_at"] = $currentTime;
                $id = WPaidVacation::query()->insertGetId($arrayInsert);
                if ($id) {
                    $this->registerWApprovalStatus($id,$listLevel,$approval_levels_step_1);

                    $this->registerWfAdditionalNotice($id,$listWfAdditionalNotice);
                }
                $this->getListMailRegisterOrEdit($arrayInsert,$approval_levels_step_1,$listWfAdditionalNotice,$mailTo, $mailCC);
            }else{
                $id = $data['id'];
                $mWPaidVacation = new WPaidVacation();
                $vacationInfo = $mWPaidVacation->getInfoByID($id);
                if($approval_fg==1) {
                    $this->handleApproval($id,$listWfAdditionalNotice,$arrayInsert,$vacationInfo->applicant_id,$vacationInfo->mail,$mailTo);
                }else{
                    $this->handleReject($id,$listWfAdditionalNotice,$arrayInsert,$vacationInfo->applicant_id,$vacationInfo->mail,$mailTo);
                }
            }
            DB::commit();
            $this->handleMail($id,$configMail,$mailTo,$mailCC,$id_before);
            if(isset( $data["id"])){
                $this->backHistory();
                if($mode=='edit'){
                    \Session::flash('message',Lang::get('messages.MSG04002'));
                }else{
                    if($approval_fg==1){
                        \Session::flash('message',Lang::get('messages.MSG10017'));
                    }
                    if($approval_fg==0){
                        \Session::flash('message',Lang::get('messages.MSG10020'));
                    }
                }
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
        $field = ['[id]','[applicant_id]','[approval_kb]','[start_date]','[end_date]','[days]','[times]','[reasons]','[id_before]','[title]','[send_back_reason]'];
        $data['id_before'] = $id_before;
        $text = str_replace($field, [$data['id'],$data['staff_nm'],$data['approval_kb'],$data['start_date'],$data['end_date'],$data['days'],$data['times'],$data['reasons'],$data['id_before'],$data['title'],$data['send_back_reason']],
            $configMail['template']);
        $subject = str_replace(['[id]','[approval_kb]','[applicant_id]','[applicant_office_id]'],[$data['id'],$data['approval_kb'],$data['staff_nm'],$data['applicant_office_id']],$configMail["subject"]);
        $this->sendMail($configMail,$mailTo,$mailCC,$subject,$text);
    }

}
