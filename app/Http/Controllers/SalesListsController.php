<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesListsController extends Controller
{
    use ListTrait;
    public $table = "t_saleses";
    protected function search($data){
        $currentDate = date("Y-m-d",time());
        $dataSearch=$data['fieldSearch'];
        $this->query->select(
            't_saleses.daily_report_date'
        );
//        $this->query->leftJoin('mst_business_offices', function ($join) {
//            $join->on('mst_business_offices.id', '=', 'sales_lists.regist_office_id');
//        });
        $this->query->where('t_saleses.deleted_at',null);

            $this->query->orderBy('t_saleses.created_at','desc');
    }

    public function index(Request $request){
        $fieldShowTable = [
            'daily_report_date' => [
                "classTH" => "wd-100",
            ],
            'mst_customers_cd' => [
                "classTH" => "min-wd-100",
            ],
            'customer_nm' => [
                "classTH" => "min-wd-100",
            ],
            'departure_point_name' => [
                "classTH" => "",
            ],
            'delivery_destination' => [
                "classTH" => "min-wd-100",
            ],
            'payment' => [
                "classTH" => "min-wd-100",
            ],
            'consumption_tax' => [
                "classTH" => "wd-120",
                "classTD" => "text-center",
            ],
            'total_fee' => [
                "classTH" => "wd-120",
                "classTD" => "text-center",
            ],
            'last_updated' => [
                "classTH" => "wd-120",
                "classTD" => "text-center",
            ],

        ];
        $mBussinessOffice = new MBusinessOffices();
        $businessOffices = $mBussinessOffice->getAllData();
        return view('sales_lists.index',[
            'fieldShowTable'=>$fieldShowTable,
            'businessOffices'=> $businessOffices,
           ]);
    }


}
