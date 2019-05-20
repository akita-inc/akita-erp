<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
        return config('params.page_size_work_flow');
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
            DB::raw("DATE_FORMAT(wf_paid_vacation.regist_date,'%d/%m/%Y') as applicant_date"),
            DB::raw('CONCAT_WS("    ",ms.last_nm,ms.first_nm) as applicant_nm'),
            'mbo.business_office_nm as sales_office',
            'mgp.date_nm as vacation_class',
            DB::raw("
            CASE	
                WHEN wf_paid_vacation.start_date = wf_paid_vacation.end_date THEN
                DATE_FORMAT(wf_paid_vacation.start_date,'%d/%m/%Y')
                ELSE CONCAT_WS( '～', DATE_FORMAT(wf_paid_vacation.start_date,'%d/%m/%Y'), DATE_FORMAT(wf_paid_vacation.end_date,'%d/%m/%Y')) 
            END AS period,
            CASE		
                WHEN ( wf_paid_vacation.days = 0 AND wf_paid_vacation.times = 0 ) THEN
                '' 
                WHEN ( wf_paid_vacation.days <> 0 AND wf_paid_vacation.times = 0 ) THEN
                CONCAT( wf_paid_vacation.days, '日' ) 
                WHEN ( wf_paid_vacation.days = 0 AND wf_paid_vacation.times <> 0 ) THEN
                CONCAT( wf_paid_vacation.times, '時間' )
                ELSE CONCAT( wf_paid_vacation.days, '日 ', wf_paid_vacation.times, '時間' ) 
            END AS days,
            CASE		
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_paid_vacation.id AND approval_fg <> 1 ) = 0 ) THEN
                '承認済み' 
                ELSE 
                CONCAT((
                    SELECT
                        mgp.date_nm 
                    FROM
                        wf_approval_status was
                        JOIN mst_general_purposes mgp ON was.approval_levels = mgp.date_id 
                    WHERE
                        was.wf_type_id = 1 
                        AND was.wf_id = wf_paid_vacation.id 
                        AND mgp.data_kb = '12001' 
                    ORDER BY
                        was.approval_steps,
                        was.id 
                        LIMIT 1 
                    ) ,' 承認待ち'
                )
	        END AS approval_status
            ")
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
            $this->query->join('wf_approval_status as was', function($join){
                $join->on('was.wf_id','=','wf_paid_vacation.id')
                    ->where('was.wf_type_id',1)
                ->where('was.approval_fg',0);
            });
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
                $orderCol .= " DESC";
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
                "sortBy"=>"name"
            ],
            'applicant_nm' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ],
            'sales_office' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ],
            'vacation_class' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ],
            'period' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ],
            'days' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
            ],
            'approval_status' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"steps"
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
        return view('work_flow.form', [

        ]);
    }


}
