<?php

namespace App\Http\Controllers;


use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MEmptyInfo;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class EmptyInfoController extends Controller {
    use ListTrait,FormTrait;
    public $table = "empty_info";
    public $allNullAble = false;
    public $beforeItem = null;

    public $ruleValid = [
        'mst_customers_cd'  => 'required|one_bytes_string|length:5',
        'adhibition_start_dt'  => 'required',
        'discount_rate'  => 'nullable|one_byte_number|length:3',
        'customer_nm'  => 'required|nullable|length:200',
        'customer_nm_kana'  => 'kana|nullable|length:200',
        'customer_nm_formal'  => 'length:200|nullable',
        'customer_nm_kana_formal'  => 'kana|nullable|length:200',
        'customer_nm_kana_formal'  => 'kana|nullable|length:200',
        'person_in_charge_last_nm'  => 'length:25|nullable',
        'person_in_charge_first_nm'  => 'length:25|nullable',
        'person_in_charge_last_nm_kana'  => 'kana|nullable|length:50',
        'person_in_charge_first_nm_kana'  => 'kana|nullable|length:50',
        'zip_cd'  => 'zip_code|nullable|length:7',
        'address1'  => 'nullable|length:20',
        'address2'  => 'nullable|length:20',
        'address3'  => 'nullable|length:50',
        'phone_number'  => 'phone_number|nullable|length:20',
        'fax_number'  => 'fax_number|nullable|length:20',
        'hp_url'  => 'nullable|length:2500',
        'explanations_bill'  => 'nullable|length:100',
        'bundle_dt'  => 'one_byte_number|nullable|length:2',
        'deposit_day'  => 'one_byte_number|nullable|between_custom:1,31|length:2',
        'deposit_method_notes'  => 'nullable|length:200',
        'deposit_bank_cd'  => 'nullable|length:4',
        'notes'  => 'nullable|length:50',
    ];

    public $labels = [];

    public $messagesCustom = [];

    public function __construct(){
        $this->labels = Lang::get("empty_info.create.field");
        parent::__construct();
    }
    protected function search($data){
            $this->query->select('mst_business_offices.business_office_nm as regist_office'
//            'empty_info.vehicles_cd',
//            'empty_info.door_number',
//            'empty_info.registration_numbers'
//            DB::raw("DATE_FORMAT(empty_info.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
//            DB::raw("DATE_FORMAT(empty_info.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
//            DB::raw("DATE_FORMAT(empty_info.modified_at, '%Y/%m/%d') as modified_at")
//            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
            );

            $this->query->leftJoin('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'empty_info.vehicle_size');
            })->leftjoin('mst_general_purposes', function ($join) {
                $join->on('mst_general_purposes.date_id', '=', 'empty_info.vehicle_size')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb.vehicles_kb'));
            });
////            ->leftjoin(DB::raw('(select vehicles_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from empty_info where deleted_at IS NULL group by vehicles_cd) sub'), function ($join) {
////                $join->on('sub.vehicles_cd', '=', 'empty_info.vehicles_cd');
////            })
////            ->whereRaw('empty_info.deleted_at IS NULL')
////            ->where('empty_info.vehicles_cd', "LIKE", "%{$where['suppliers_cd']}%");
//
////        if ($where['radio_reference_date'] == '1' && $where['reference_date'] != '') {
////            $this->query->where('empty_info.adhibition_start_dt', "<=", $where['reference_date']);
////            $this->query->where('empty_info.adhibition_end_dt', ">=", $where['reference_date']);
////        }
//
//        $this->query->orderby('empty_info.vehicles_cd');
//        $this->query->orderby('empty_info.adhibition_start_dt');
    }
    public function store(Request $request, $id=null){
        $mEmptyInfo = new MEmptyInfo();
        return view('empty_info.form', [
            '$mEmptyInfo' => $mEmptyInfo,
        ]);
    }
    public function index(Request $request){
        $fieldShowTable = [
            'regist_office' => [
                "classTH" => "wd-120"
            ],
            'vehicle_classification'=> [
                "classTH" => ""
            ],
            'registration_numbers'=> [
                "classTH" => ""
            ],
            'car_compartment'=> [
                "classTH" => "",
                "classTD" => "td-nl2br",
            ],
            'vehicle_body_shape'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'max_load_capacity'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'equipment'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'schedule_date'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'asking_price'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'asking_baggage'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'expected_location'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],
            'arrive_date'=> [
                "classTH" => "wd-120",
                "classTD" => "text-center"
            ],

        ];
        $mBussinessOffice = new MBusinessOffices();
        $mGeneralPurpose = new MGeneralPurposes();
        $askingBaggages = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['asking_baggage']);
        $startPrefCds = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['prefecture_cd']);
        $businessOffices = $mBussinessOffice->getAllData();
        return view('empty_info.index',[
                                    'fieldShowTable'=>$fieldShowTable,
                                    'businessOffices'=> $businessOffices,
                                    'askingBaggages'=>$askingBaggages,
                                    'startPrefCds'=>$startPrefCds]);
    }

}