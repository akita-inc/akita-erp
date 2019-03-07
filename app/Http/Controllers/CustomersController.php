<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:11 PM
 */
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CustomersController extends Controller
{
    use ListTrait;
    public $table = "mst_customers";

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $reference_date=date('Y-m-d', strtotime($dataSearch['reference_date']) );
        $this->query->select(
            'mst_customers.mst_customers_cd',
            'mst_customers.customer_nm',
            'mst_customers.customer_nm_kana',
            DB::raw('CONCAT(mst_general_purposes.date_nm,mst_customers.address1,mst_customers.address2,mst_customers.address3) as street_address'),
            'mst_customers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_customers.adhibition_start_dt, '%Y/%m/%d ') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_customers.adhibition_end_dt, '%Y/%m/%d ') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_customers.modified_at, '%Y/%m/%d %T') as modified_at")
        );
        $this->query->leftJoin('mst_general_purposes', function ($join) {
            $join->on("data_kb", "=", DB::raw(config("params.data_kb.prefecture")));
            $join->on("date_id", "=", "mst_customers.prefectures_cd");
        })->where('mst_customers.mst_customers_cd', 'LIKE', '%' . $dataSearch['mst_customers_cd'] . '%')
            ->where('mst_customers.customer_nm', 'LIKE', '%' . $dataSearch['customer_nm'] . '%');

        if ($dataSearch['status'] == '1' && $reference_date!=null) {
            $this->query->where('mst_customers.adhibition_start_dt','<=',$reference_date)
                        ->where('mst_customers.adhibition_end_dt','>=',$reference_date);
        }

    }

    public function index(Request $request){
        $fieldShowTable = [
            'mst_customers_cd' => [
                "classTH" => "wd-100"
            ],
            'customer_nm'=> [
                "classTH" => ""
            ],
            'street_address'=> [
                "classTH" => ""
            ],
            'explanations_bill'=> [
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
        return view('customers.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function create(Request $request){

        return view('customers.create');
    }
}
