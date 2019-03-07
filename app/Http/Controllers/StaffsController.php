<?php
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class StaffsController extends Controller
{
    use ListTrait;
    public $table = "mst_staffs";

    protected function search($data){
        $this->query->select(
            'mst_staffs.staff_cd',
            DB::raw('CONCAT(mst_staffs.last_nm,"  ",first_nm) as staff_nm'),
            DB::raw('CONCAT(mst_staffs.last_nm_kana,"  ",first_nm_kana) as staff_nm_kana'),
            'mst_general_purposes.date_id',
            'mst_general_purposes.date_nm',
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_start_dt, '%Y/%m/%d ') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.adhibition_end_dt, '%Y/%m/%d ') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_staffs.modified_at, '%Y/%m/%d %T') as modified_at")
        );
        $this->query->leftJoin('mst_general_purposes', function ($join) {
            $join->on('mst_general_purposes.date_id', '=', 'mst_staffs.employment_pattern_id');

        });
    }

    public function index(Request $request){
        $fieldShowTable = [
            'staff_cd' => [
                "classTH" => "wd-100"
            ],
            'employment_pattern_id'=> [
                "classTH" => ""
            ],
            'position_id'=> [
                "classTH" => ""
            ],
            'staff_nm'=> [
                "classTH" => ""
            ],
            'belong_company_id'=> [
                "classTH" => ""
            ],
            'mst_business_office_id'=> [
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
            ]

        ];
        return view('staffs.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function create(Request $request){

        return view('staffs.create');
    }
}
