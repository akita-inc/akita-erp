<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 3/5/2019
 * Time: 4:11 PM
 */
namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MAccountTitles;
use App\Models\MBusinessOffices;
use App\Models\MCustomers;
use App\Models\MCustomersCategories;
use App\Models\MGeneralPurposes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
class CustomersController extends Controller
{
    use ListTrait,FormTrait;
    public $table = "mst_customers";

    public $ruleValid = [
        'mst_customers_cd'  => 'required|one_bytes_string|length:5',
        'adhibition_start_dt'  => 'required',
        'customer_nm'  => 'nullable|length:200',
        'customer_nm_kana'  => 'kana|nullable',
        'customer_nm_formal'  => 'length:200|nullable',
        'customer_nm_kana_formal'  => 'kana|nullable'
    ];

    public $messagesCustom = [];

    protected function search($data){
        $dataSearch=$data['fieldSearch'];
        $reference_date=date('Y-m-d', strtotime($dataSearch['reference_date']) );
        $this->query->select('mst_customers.id',
            'mst_customers.mst_customers_cd',
            'mst_customers.customer_nm',
            'mst_customers.customer_nm_kana',
            DB::raw("CONCAT_WS('',mst_general_purposes.date_nm,mst_customers.address1,mst_customers.address2,mst_customers.address3) as street_address"),
            'mst_customers.explanations_bill',
            DB::raw("DATE_FORMAT(mst_customers.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_customers.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_customers.modified_at, '%Y/%m/%d') as modified_at"),
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
        );
        $this->query->leftJoin('mst_general_purposes', function ($join) {
            $join->on("data_kb", "=", DB::raw(config("params.data_kb.prefecture_cd")));
            $join->on("date_id", "=", "mst_customers.prefectures_cd");
        })
        ->leftjoin(DB::raw('(select mst_customers_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_customers where deleted_at IS NULL group by mst_customers_cd) sub'), function ($join) {
            $join->on('sub.mst_customers_cd', '=', 'mst_customers.mst_customers_cd');
        })
        ->whereRaw('mst_customers.deleted_at IS NULL')
        ->where('mst_customers.mst_customers_cd', 'LIKE', '%' . $dataSearch['mst_customers_cd'] . '%')
        ->where('mst_customers.customer_nm', 'LIKE', '%' . $dataSearch['customer_nm'] . '%');

        if ($dataSearch['status'] == '1' && $reference_date!=null) {
            $this->query->where('mst_customers.adhibition_start_dt','<=',$reference_date)
                        ->where('mst_customers.adhibition_end_dt','>=',$reference_date);
        }

        $this->query->orderby('mst_customers.mst_customers_cd');
        $this->query->orderby('mst_customers.adhibition_start_dt');
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
                "classTH" => "",
                "classTD" => "td-nl2br",
            ],
            'adhibition_start_dt'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'adhibition_end_dt'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'modified_at'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ]
        ];
        return view('customers.index',[ 'fieldShowTable'=>$fieldShowTable ]);
    }

    public function delete($id)
    {
        $mCustomers = new MCustomers();
        if ($mCustomers->deleteCustomer($id)) {
            $response = ['data' => 'success'];
        } else {
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExist($id){
        $mCustomers = new MCustomers();
        $mCustomers = $mCustomers->find($id);
        if (isset($mCustomers)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
        }
    }

    public function create(Request $request){
        $mGeneralPurposes = new MGeneralPurposes();
        $mCustomerCategories=new MCustomersCategories();
        $mBusinessOffices=new MBusinessOffices();
        $mAccountTitles=new MAccountTitles();
        $customer_categories=$mCustomerCategories->getListCustomerCategories();
        $mBusinessOffices=$mBusinessOffices->getListBusinessOffices();
        $listAccountTitles=$mAccountTitles->getListAccountTitles();
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        $listDepositMethods=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['deposit_method'],'');
        $listDepositMonths=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['deposit_month'],'');
        $listConsumptionTaxCalcUnit=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['consumption_tax_calc_unit'],'');
        $listRoundingMethod=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['rounding_method'],'');
        $listDepositBankCd=$mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['deposit_bank_cd'],'');
        return view('customers.create', [
                                'listPrefecture' => $listPrefecture,
                                'customer_categories'=>$customer_categories,
                                'business_offices'=>$mBusinessOffices,
                                'listDepositMethods'=>$listDepositMethods,
                                'listDepositMonths'=>$listDepositMonths,
                                'listConsumptionTaxCalcUnit'=>$listConsumptionTaxCalcUnit,
                                'listRoundingMethod'=>$listRoundingMethod,
                                'listDepositBankCd'=>$listDepositBankCd,
                                'listAccountTitles'=>$listAccountTitles
        ]);
    }
}
