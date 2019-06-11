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

class ExpenseEntertainmentController extends Controller
{
    use ListTrait,FormTrait,WorkflowTrait;
    public $table = "wf_business_entertaining_expenses";
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
        $this->wf_type_id = config('params.expense_entertainment_wf_type_id_default');

    }
    protected function getPaging()
    {
        return config('params.page_size_expense_entertainment');
    }

    protected function search($data){
        $where = array(
            'applicant_id' => $data['fieldSearch']['applicant_id'],
            'applicant_nm' => $data['fieldSearch']['applicant_nm'],
            'mst_business_office_id' => $data['fieldSearch']['applicant_office_id'],
            'client_company_name' => $data['fieldSearch']['client_company_name'],
            'show_status' =>$data['fieldSearch']['show_status'],
            'show_deleted' =>$data['fieldSearch']['show_deleted'],
        );
        $this->query->select(
            'wf_business_entertaining_expenses.id',
            DB::raw("DATE_FORMAT(wf_business_entertaining_expenses.regist_date,'%Y/%m/%d') as regist_date"),
            'ms.staff_cd as staff_cd',
            DB::raw('CONCAT_WS("    ",ms.last_nm,ms.first_nm) as applicant_nm'),
            'offices.business_office_nm as applicant_office_id',
            'wf_business_entertaining_expenses.date',
            'wf_business_entertaining_expenses.client_company_name',
            DB::raw('CONCAT_WS("",wf_business_entertaining_expenses.client_members_count,"名 / ",wf_business_entertaining_expenses.own_members_count,"名") as participant_amount'),
            'wf_business_entertaining_expenses.cost',
            'wf_business_entertaining_expenses.delete_at',
            DB::raw("
            CASE	
                WHEN (( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id) = 0) THEN
                '---'	
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 ) = 0 ) THEN
               '承認済み' 
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
		        '却下'
            ELSE 
                CONCAT(
                    COALESCE((
                    SELECT
                        was.title
                    FROM
                        wf_approval_status was			
                    WHERE
                        was.wf_type_id = ".config('params.expense_wf_type_id_default')."
                        AND was.wf_id = wf_business_entertaining_expenses.id
                        AND was.approval_fg = 0
                    ORDER BY
                        was.approval_steps,
                        was.id 
                        LIMIT 1 
                    ),''),' 承認待ち' 
                ) 
	        END AS approval_status,
	        CASE -- approval_fg
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_fg,
            CASE -- approval_levels
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining_expenses.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining_expenses.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_levels
            ")
        );
        $this->query->join('mst_staffs as ms', function($join){
            $join->on('ms.staff_cd','=','wf_business_entertaining_expenses.applicant_id')
                ->whereRaw('ms.deleted_at IS NULL');
        })->join('mst_business_offices as offices', function($join){
            $join->on('offices.id','=','wf_business_entertaining_expenses.applicant_office_id')
                ->whereRaw('offices.deleted_at IS NULL');
        });
        if(Auth::user()->approval_levels === null){
            $this->query->where('wf_business_entertaining_expenses.applicant_id','=',Auth::user()->staff_cd);
        }
        if($where['applicant_id']!='')
        {
            $this->query->where('wf_business_entertaining_expenses.id','LIKE','%' .$where['applicant_id'].'%');
        }
        //
        if($where['applicant_nm']!='')
        {
            $this->query->where( DB::raw('CONCAT(ms.last_nm,ms.first_nm)'), 'LIKE', '%'.$where['applicant_nm'].'%');
        }
        if($where['mst_business_office_id']!='')
        {
            $this->query->where('wf_business_entertaining_expenses.applicant_office_id',$where['mst_business_office_id']);
        }
        if($where['client_company_name']!='')
        {
            $this->query->where( 'wf_business_entertaining_expenses.client_company_name', 'LIKE', '%'.$where['client_company_name'].'%');
        }
        if($where['show_status']!=true)
        {
            $this->query->whereRaw('(SELECT COUNT(*)
                                    FROM wf_approval_status
                                    WHERE wf_id = wf_business_entertaining_expenses.id AND wf_type_id = '.config('params.expense_wf_type_id_default').' 
                                    AND approval_fg = 0) > 0');
        }
        if($where['show_deleted']!=true)
        {
            $this->query->whereNull('delete_at');
        }
        if ($data["order"]["col"] != '') {
            $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                if($orderCol  == 'client_members_count,own_members_count'){
                    $orderCol = 'wf_business_entertaining_expenses.client_members_count DESC,wf_business_entertaining_expenses.own_members_count DESC';
                }
                else if($orderCol  == 'approval_fg,approval_levels'){
                    $orderCol = 'approval_fg DESC,approval_levels DESC';
                }
                else if($orderCol  == 'applicant_office_id'){
                    $orderCol = 'offices.business_office_nm DESC';
                }
                else{
                    $orderCol .= " DESC";
                }
            }
            $this->query->orderbyRaw($orderCol);
        }
        $this->query->orderBy('wf_business_entertaining_expenses.id','desc');

    }

    public function index(Request $request){
        $fieldShowTable = [
            'id' => [
                "classTH" => "wd-100",
                "classTD" => "text-center",
                "sortBy"=>"id"
            ],
            'regist_date' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"regist_date"
            ],
            'applicant_nm' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"applicant_nm_kana"
            ],
            'applicant_office_id' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"business_office_id"
            ],
            'date' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"date"
            ],
            'client_company_name' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"client_company_name"
            ],
            'participant_amount' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"client_members_count,own_members_count"
            ],
            'cost' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"cost"
            ],
            'approval_status' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"approval_fg,approval_levels"
            ],
        ];
        $mBussinessOffice = new MBusinessOffices();
        $businessOffices = $mBussinessOffice->getAllData();
        return view('expense_entertainment.index',[
            'fieldShowTable'=>$fieldShowTable,
            'businessOffices' => $businessOffices,
        ]);
    }

    public function store(Request $request, $id=null){
        $mWFBusinessEntertain = null;
        $mode = "register";
        $role = 1;
        $mWApprovalStatus = new WApprovalStatus();
        if($id != null){
            $mWFBusinessEntertain = new WFBusinessEntertaining();
            $mWFBusinessEntertain = $mWFBusinessEntertain->getInfoByID($id);
            if(empty($mWFBusinessEntertain)){
                abort('404');
            }else{
                $mWFBusinessEntertain = $mWFBusinessEntertain->toArray();
                $this->checkAuthentication($id,$mWFBusinessEntertain,$request, $mode,$role);
            }
        }
        $arrayStore = $this->beforeStore($id);
        $mGeneralPurposes = new MGeneralPurposes();
        $listDepositClassification= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.wf_expense_app_temporary_payment'),'Empty');
        $currentDate = date('Y/m/d');
        return view('expense_entertainment.form', array_merge($arrayStore,[
            'mWFBusinessEntertain' => $mWFBusinessEntertain,
            'listDepositClassification' => $listDepositClassification,
            'currentDate' => $currentDate,
            'role' => $role,
            'mode' => $mode,
        ]));
    }
    public function beforeSubmit($data){
        if(isset($data['deposit_flg']) && $data['deposit_flg']==1 )
        {
            $this->ruleValid['deposit_amount'] = 'required|decimal_custom|length:8';
        }
    }
    protected function validAfter( &$validator,$data ){
        $listWfAdditionalNotice = $data['wf_additional_notice'];
        $this->validateWfAdditionalNotice($validator,$listWfAdditionalNotice);
    }

    protected function save($data){
        $id_before =  null;
        $mGeneralPurposes = new MGeneralPurposes();
        $listLevel= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.wf_level'),'Empty');
        $arrayInsert = $data;
        $listWfAdditionalNotice = $arrayInsert['wf_additional_notice'];
        $approval_fg = $arrayInsert["approval_fg"];
        $currentTime = date("Y-m-d H:i:s",time());
        $arrayInsert['regist_date'] = $currentTime;
        $send_back_reason=$arrayInsert["send_back_reason"];
        $mode = $arrayInsert["mode"];
        unset($arrayInsert["id"]);
        unset($arrayInsert["mode"]);
        unset($arrayInsert["staff_nm"]);
        unset($arrayInsert["applicant_office_nm"]);
        unset($arrayInsert["wf_additional_notice"]);
        unset($arrayInsert["approval_fg"]);
        unset($arrayInsert["send_back_reason"]);
        $mWApprovalStatus = new WApprovalStatus();
        $mailCC = [];
        $mailTo = [];
        DB::beginTransaction();
        try{
            if(isset( $data["id"]) && $data["id"]){
                $arrayInsert["modified_at"] = $currentTime;
                if($mode=='edit'){
                    $id_before = $data["id"];
                    WFBusinessEntertaining::query()->where("id","=",$id_before)->update(['delete_at' => date("Y-m-d H:i:s",time())]);
                    $configMail = Lang::get('mail_template.expense_entertainment_edit_mail');
                }
                else{
                    if($approval_fg==1){
                        $mWApprovalStatus->approvalVacation($data["id"], $this->wf_type_id, $currentTime);
                        $configMail = Lang::get('mail_template.expense_entertainment_approval_mail');
                    }
                    if($approval_fg==0){
                        $mWApprovalStatus->rejectVacation($data["id"],  $this->wf_type_id,$currentTime,$send_back_reason);
                        $configMail = Lang::get('mail_template.expense_entertainment_approval_mail');
                    }
                }
            }
            else{
                $configMail = Lang::get('mail_template.expense_entertainment_register_mail');
            }
            if($mode=='register' || $mode=='edit') {
                $approval_levels_step_1 = "";
                $arrayInsert["create_at"] = $currentTime;
                $arrayInsert["modified_at"] = $currentTime;
                $id = WFBusinessEntertaining::query()->insertGetId($arrayInsert);
                if ($id) {
                    $this->registerWApprovalStatus($id,$listLevel,$approval_levels_step_1);
                    $this->registerWfAdditionalNotice($id,$listWfAdditionalNotice);
                }
                $this->getListMailRegisterOrEdit($arrayInsert,$approval_levels_step_1,$listWfAdditionalNotice,$mailTo, $mailCC);
            }else{
                $id = $data['id'];
                $mWFBusinessEntertaining = new WFBusinessEntertaining();
                $mBusinessEntertainInfo = $mWFBusinessEntertaining->getInfoByID($id);
                if($approval_fg==1) {
                    $this->handleApproval($id,$listWfAdditionalNotice,$arrayInsert,$mBusinessEntertainInfo->applicant_id,$mBusinessEntertainInfo->mail,$mailTo);
                }else{
                    $this->handleReject($id,$listWfAdditionalNotice,$arrayInsert,$mBusinessEntertainInfo->applicant_id,$mBusinessEntertainInfo->mail,$mailTo);
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
        $mWFBusinessEntertaining=new WFBusinessEntertaining();
        $data = $mWFBusinessEntertaining->getInfoForMail($id);
        $field = ['[id]','[applicant_id]','[applicant_office_id]','[date]','[client_company_name]','[client_members]','[client_members_count]','[own_members]','[own_members_count]','[place]','[conditions]','[purpose]','[deposit_flg]','[deposit_amount]'];
        $data['id_before'] = $id_before;
        $text = str_replace($field, [$data['id'],$data['applicant_id'],$data['applicant_office_id'],$data['date'],$data['client_company_name'],$data['client_members'],$data['client_members_count'],$data['own_members'],$data['own_members_count'],$data['place'],$data['conditions'],$data['purpose'],$data['deposit_flg'],$data['deposit_amount']],$configMail['template']);
        $subject = str_replace(['[id]','[applicant_id]','[applicant_office_id]'],[$data['id'],$data['applicant_id'],$data['applicant_office_id']],$configMail["subject"]);
        $this->sendMail($configMail,$mailTo,$mailCC,$subject,$text);
    }

}
