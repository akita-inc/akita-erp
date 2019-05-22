<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\WApprovalStatus;
use App\Models\WPaidVacation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
class TakeVacationController extends Controller
{
    use ListTrait;
    public $table = "wf_paid_vacation";
    public $ruleValid = [

    ];
    public $messagesCustom =[];
    public $labels=[];
    public $currentData=null;
    public function __construct(){
        parent::__construct();

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
                ORDER BY id
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
                ORDER BY id
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
                "sortBy"=>"staff_cd"
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
            if($data->delete_at == null){
                $return['mode'] = 'reference';
            }
            else{
                if($approvalStatus->count() >= 1){
                    if($data->applicant_id == Auth::user()->staff_cd){//1-1-1. 申請者＝ログインID
                        $return['mode'] = 'edit';
                    }
                    else{//1-1-2.  申請者 != ログインID
                        if($WApprovalStatus::where(['wf_id'=>$data->id,'approval_fg'=>0,'approval_levels'=>Auth::user()->approval_levels])->count() > 0){// 1-1-2-1. ログイン者＝承認権限を持っている（mst_staffs.approval_levels is not null）かつ、その承認レベルが未承認である。
                            $return['mode'] = 'approve';
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
        $mWPaidVacation = null;
        $mode = "register";
        $role = 1;
        if($id != null){
            $mWPaidVacation = WPaidVacation::find( $id );
            if(empty($mWPaidVacation)){
                abort('404');
            }else{
                $mWPaidVacation = $mWPaidVacation->toArray();
                $routeName = $request->route()->getName();
                switch ($routeName){
                    case 'empty_info.approval':
                        $mode = 'approval';
                        if(($mWPaidVacation['status']==1 || $mWPaidVacation['status']==2 ) && $mWPaidVacation['regist_office_id']== Auth::user()->mst_business_office_id ){
                            $role = 2; // no authentication
                        }
                        break;
                    default:
                        $mode ='edit';
                        if($mWPaidVacation['status']!=1 || $mWPaidVacation['regist_office_id']!= Auth::user()->mst_business_office_id ){
                            $role = 2; // no authentication
                        }
                        break;
                }
            }
        }
        $mBusinessOffices = new MBusinessOffices();
        $mGeneralPurposes = new MGeneralPurposes();
        $businessOfficeNm = $mBusinessOffices->select('business_office_nm')->where('id','=',Auth::user()->mst_business_office_id)->first();
        $listVacationIndicator= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb.vacation_indicator'),'Empty');
        $listVacationAcquisitionTimeIndicator= $mGeneralPurposes->getInfoByDataKB(config('params.data_kb.vacation_acquisition_time_indicator'));
        return view('take_vacation.form', [
            'mWPaidVacation' => $mWPaidVacation,
            'businessOfficeNm' => $businessOfficeNm,
            'listVacationIndicator' => $listVacationIndicator,
            'listVacationAcquisitionTimeIndicator' => $listVacationAcquisitionTimeIndicator,
            'role' => $role,
            'mode' => $mode
        ]);
    }


}
