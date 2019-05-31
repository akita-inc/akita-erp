<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSaleses;
use Illuminate\Support\Facades\Auth;
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
        date_default_timezone_set("Asia/Tokyo");
        parent::__construct();

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
//        dd($where);
        //select
        $this->query->select(
            'wf_business_entertaining.id',
            DB::raw("DATE_FORMAT(wf_business_entertaining.regist_date,'%Y/%m/%d') as regist_date"),
            'ms.staff_cd as staff_cd',
            DB::raw('CONCAT_WS("    ",ms.last_nm,ms.first_nm) as applicant_nm'),
            'offices.business_office_nm as applicant_office_id',
            'wf_business_entertaining.date',
            'wf_business_entertaining.client_company_name',
            DB::raw('CONCAT_WS("",wf_business_entertaining.client_members_count,"名 / ",wf_business_entertaining.own_members_count,"名") as participant_amount'),
            'wf_business_entertaining.cost',
            'wf_business_entertaining.delete_at',
            DB::raw("
            CASE	
                WHEN (( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id) = 0) THEN
                '---'	
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 ) = 0 ) THEN
               '承認済み' 
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
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
                        AND was.wf_id = wf_business_entertaining.id
                        AND was.approval_fg = 0
                    ORDER BY
                        was.approval_steps,
                        was.id 
                        LIMIT 1 
                    ),''),' 承認待ち' 
                ) 
	        END AS approval_status,
	        CASE -- approval_fg
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_fg
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_fg,
            CASE -- approval_levels
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 ) = 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                ORDER BY id
                LIMIT 1)-- 承認済み
                
                WHEN ( ( SELECT count( approval_fg ) FROM wf_approval_status WHERE wf_id = wf_business_entertaining.id AND approval_fg <> 1 AND approval_fg <> 0) > 0 ) THEN
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                AND approval_fg = 2
                ORDER BY id
                LIMIT 1)-- 却下
                
                ELSE
                (SELECT  approval_levels
                FROM wf_approval_status 
                WHERE wf_id = wf_business_entertaining.id
                AND approval_fg = 0
                ORDER BY approval_steps,id
                LIMIT 1)
            END AS approval_levels
            ")
        );
        //join
        $this->query->join('mst_staffs as ms', function($join){
            $join->on('ms.staff_cd','=','wf_business_entertaining.applicant_id')
                ->whereRaw('ms.deleted_at IS NULL');
        })->join('mst_business_offices as offices', function($join){
            $join->on('offices.id','=','wf_business_entertaining.applicant_office_id')
                ->whereRaw('offices.deleted_at IS NULL');
        });
        if(Auth::user()->approval_levels === null){
            $this->query->where('wf_business_entertaining.applicant_id','=',Auth::user()->staff_cd);
        }
        if($where['applicant_id']!='')
        {
            $this->query->where('wf_business_entertaining.id','LIKE','%' .$where['applicant_id'].'%');
        }
        //
        if($where['applicant_nm']!='')
        {
            $this->query->where( DB::raw('CONCAT(ms.last_nm,ms.first_nm)'), 'LIKE', '%'.$where['applicant_nm'].'%');
        }
        if($where['mst_business_office_id']!='')
        {
            $this->query->where('wf_business_entertaining.applicant_office_id',$where['mst_business_office_id']);
        }
        if($where['client_company_name']!='')
        {
            $this->query->where( 'wf_business_entertaining.client_company_name', 'LIKE', '%'.$where['client_company_name'].'%');
        }
        if($where['show_status']!=true)
        {
            $this->query->whereRaw('(SELECT COUNT(*)
                                    FROM wf_approval_status
                                    WHERE wf_id = wf_business_entertaining.id
                                    AND wf_type_id = '.config('params.expense_wf_type_id_default').' 
                                    AND approval_fg = 0) > 0');
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
                if($orderCol  == 'client_members_count,own_members_count'){
                    $orderCol = 'wf_business_entertaining.client_members_count DESC,wf_business_entertaining.own_members_count DESC';
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
        $this->query->orderBy('wf_business_entertaining.id','desc');

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
                "sortBy"=>"applicant_nm"
            ],
            'applicant_office_id' => [
                "classTH" => "min-wd-100",
                "classTD" => "text-center",
                "sortBy"=>"applicant_office_id"
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
        $mGeneralPurpose = new MGeneralPurposes();
        $mBussinessOffice = new MBusinessOffices();
        $businessOffices = $mBussinessOffice->getAllData();
        return view('expense_application.index',[
            'fieldShowTable'=>$fieldShowTable,
            'businessOffices' => $businessOffices,
        ]);
    }


}
