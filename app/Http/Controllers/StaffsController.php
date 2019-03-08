<?php
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MStaffs;
use App\Models\MGeneralPurposes;
class StaffsController extends Controller
{
    use ListTrait;
    public $table = "mst_staffs";

    protected function search($data){
        $this->query->select(
            'mst_staffs.id',
            'mst_staffs.staff_cd',
            'employment_pattern.date_id',
            'employment_pattern.date_nm as employment_pattern_nm',
            DB::raw('CONCAT(mst_staffs.last_nm,"    ",first_nm) as staff_nm'),
            DB::raw('CONCAT(mst_staffs.last_nm_kana,"   ",first_nm_kana) as staff_nm_kana'),
            'position.date_nm as position_nm',
            'belong_company.date_nm as belong_company_nm',
            'mst_business_offices.business_office_nm',
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_start_dt, '%Y/%m/%d ') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_end_dt, '%Y/%m/%d ') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.modified_at, '%Y/%m/%d ') as modified_at")
        );
        $this->query->leftJoin('mst_general_purposes as employment_pattern', function ($join) {
            $join->on('employment_pattern.date_id', '=', 'mst_staffs.employment_pattern_id')
                ->where('employment_pattern.data_kb', config('params.data_kb')['employment_pattern']);;
        });
        $this->query->leftJoin('mst_general_purposes as position', function ($join) {
            $join->on('position.date_id', '=', 'mst_staffs.position_id')
                ->where('position.data_kb', config('params.data_kb')['position']);;
        });
        $this->query->leftJoin('mst_general_purposes as belong_company', function ($join) {
            $join->on('belong_company.date_id', '=', 'mst_staffs.belong_company_id')
                ->where('belong_company.data_kb', config('params.data_kb')['belong_company']);;
        });
        $this->query->leftJoin('mst_business_offices', function ($join) {
            $join->on('mst_business_offices.id', '=', 'mst_staffs.mst_business_office_id');

        });
        $this->query->whereRaw('mst_staffs.deleted_at IS NULL');

        $this->query->orderby('mst_staffs.staff_cd');
        $this->query->orderby('mst_staffs.adhibition_start_dt');
    }

    public function index(Request $request){
        $fieldShowTable = [
            'staff_cd' => [
                "classTH" => "wd-100"
            ],
            'employment_pattern_nm'=> [
                "classTH" => ""
            ],
            'position_nm'=> [
                "classTH" => ""
            ],
            'staff_nm'=> [
                "classTH" => ""
            ],
            'belong_company_nm'=> [
                "classTH" => ""
            ],
            'business_office_nm'=> [
                "classTH" => ""
            ],
            'adhibition_start_dt'=> [
                "classTH" => "wd-150",
                "classTD" => "text-center"
            ],
            'adhibition_end_dt'=> [
                "classTH" => "wd-150",
                "classTD" => "text-center"
            ],
            'modified_at'=> [
                "classTH" => "wd-150",
                "classTD" => "text-center"
            ],
            'delete'=>[
                "classTH" => "wd-100",
                "classTD" => "wd-50"
            ]

        ];
        return view('staffs.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function delete($id)
    {
        $mStaffs = new MStaffs();
        $mStaffs = $mStaffs->find($id);
        try
        {
            $mStaffs->delete();
            $response = ['data' => 'success'];

        } catch (\Exception $ex){
            $response = ['data' => 'failed'];
        }
        return response()->json($response);
    }

    public function create(Request $request){

        return view('staffs.create');
    }
}
