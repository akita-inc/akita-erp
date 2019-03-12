<?php
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MStaffs;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\Lang;

class StaffsController extends Controller
{
    use ListTrait;
    public $table = "mst_staffs";
    protected function search($data)
    {
        $where = array(
            'staff_cd' => $data['fieldSearch']['staff_cd'],
            'staff_nm' => $data['fieldSearch']['staff_nm'],
            'reference_date' => date('Y-m-d', strtotime($data['fieldSearch']['reference_date'])),
            'status' => $data['fieldSearch']['status'],
            'employment_pattern_id'=>$data['fieldSearch']['employment_pattern_id'],
            'position_id'=>$data['fieldSearch']['position_id'],
            'belong_company_id'=>$data['fieldSearch']['belong_company_id'],
            'mst_business_office_id'=>$data['fieldSearch']['mst_business_office_id'],
        );
        $this->query->select(
            'mst_staffs.id',
            'mst_staffs.staff_cd',
            'employment_pattern.date_id',
            'employment_pattern.date_nm as employment_pattern_nm',
            DB::raw('CONCAT(mst_staffs.last_nm,"    ",mst_staffs.first_nm) as staff_nm'),
            DB::raw('CONCAT(mst_staffs.last_nm_kana,"   ",mst_staffs.first_nm_kana) as staff_nm_kana'),
            'position.date_nm as position_nm',
            'belong_company.date_nm as belong_company_nm',
            'mst_business_offices.business_office_nm',
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.modified_at, '%Y/%m/%d') as modified_at"),
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
        );
        $this->query->leftJoin('mst_general_purposes as employment_pattern', function ($join) {
            $join->on('employment_pattern.date_id', '=', 'mst_staffs.employment_pattern_id')
                ->where('employment_pattern.data_kb', config('params.data_kb')['employment_pattern']);
        })->leftJoin('mst_general_purposes as position', function ($join) {
            $join->on('position.date_id', '=', 'mst_staffs.position_id')
                ->where('position.data_kb', config('params.data_kb')['position']);;
        })->leftJoin('mst_general_purposes as belong_company', function ($join) {
            $join->on('belong_company.date_id', '=', 'mst_staffs.belong_company_id')
                ->where('belong_company.data_kb', config('params.data_kb')['belong_company']);;
        })->leftJoin('mst_business_offices', function ($join) {
            $join->on('mst_business_offices.id', '=', 'mst_staffs.mst_business_office_id');
        })->leftjoin(DB::raw('(select staff_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_staffs where deleted_at IS NULL group by staff_cd) sub'), function ($join) {
            $join->on('sub.staff_cd', '=', 'mst_staffs.staff_cd');
        });
        $this->query->whereRaw('mst_staffs.deleted_at IS NULL');
        $this->query->where('mst_staffs.staff_cd', 'LIKE', '%' . $where['staff_cd'] . '%')
                    ->where( DB::raw('CONCAT(mst_staffs.last_nm,mst_staffs.first_nm)'), 'LIKE', '%'.$where['staff_nm'].'%');
        $this->queryDataKb('employment_pattern_id',$where['employment_pattern_id']);
        $this->queryDataKb('position_id',$where['position_id']);
        $this->queryDataKb('belong_company_id',$where['belong_company_id']);
        $this->queryDataKb('mst_business_office_id', $where['mst_business_office_id']);
        if ($where['status'] == '1' && $where['reference_date'] != null) {
            $this->query->where('mst_staffs.adhibition_start_dt','<=',$where['reference_date'])
                ->where('mst_staffs.adhibition_end_dt','>=',$where['reference_date']);
        }
        $this->query->orderby('mst_staffs.staff_cd')
                    ->orderby('mst_staffs.adhibition_start_dt');
    }

    public function index(Request $request)
    {
        $fieldShowTable = [
            'staff_cd' => [
                "classTH" => "wd-100"
            ],
            'employment_pattern_nm' => [
                "classTH" => ""
            ],
            'position_nm' => [
                "classTH" => ""
            ],
            'staff_nm' => [
                "classTH" => ""
            ],
            'belong_company_nm' => [
                "classTH" => ""
            ],
            'business_office_nm' => [
                "classTH" => ""
            ],
            'adhibition_start_dt' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'adhibition_end_dt' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'modified_at' => [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'delete' => [
                "classTH" => "wd-60",
                "classTD" => "no-padding"
            ]

        ];
        $mGeneralPurpose = new MGeneralPurposes();
        $mBussinessOffice = new MBusinessOffices();
        $employmentPatterns = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['employment_pattern']);
        $positions = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['position']);
        $belongCompanies = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['belong_company']);
        $businessOffices = $mBussinessOffice->getAllData();
        return view('staffs.index', array(
            'fieldShowTable' => $fieldShowTable,
            'belongCompanies' => $belongCompanies,
            'employmentPatterns' => $employmentPatterns,
            'positions' => $positions,
            'businessOffices' => $businessOffices
        ));
    }

    public function delete($id)
    {
        $mStaffs = new MStaffs();
        try {
            if ($mStaffs->deleteStaffs($id)) {
                $response = ['data' => 'success'];
            } else {
                $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
            }

        } catch (\Exception $ex) {
            $response = ['data' => $ex];
        }
        return response()->json($response);
    }

    public function checkIsExist($id){
        $mStaffs = new MStaffs();
        $mStaffs = $mStaffs->find($id);
        if (isset($mStaffs)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
        }
    }

    public function create(Request $request)
    {

        return view('staffs.create');
    }

}
