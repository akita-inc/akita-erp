<?php

namespace App\Http\Controllers;


use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MEmptyInfo;
use App\Models\MGeneralPurposes;
use App\Models\MVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $this->query->select('mst_business_offices.business_office_nm as regist_office',
               'vehicle_classification.date_nm as vehicle_classification',
               'empty_info.registration_numbers',
               'empty_info.vehicle_size',
               'empty_info.vehicle_body_shape',
               'empty_info.max_load_capacity',
                'empty_info.equipment',
                DB::raw("DATE_FORMAT(empty_info.start_date, '%Y/%m/%d') as adhibition_start_dt"),
                DB::raw("CONCAT_WS(' ',DATE_FORMAT(empty_info.start_date, '%Y/%m/%d'),TIME_FORMAT(empty_info.start_time,'%H:%i')) as schedule_date")

            );
            $this->query->leftJoin('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'empty_info.regist_office_id');
            })->leftjoin('mst_general_purposes as vehicle_classification', function ($join) {
                $join->on('vehicle_classification.date_id', '=', 'empty_info.vehicle_kb')
                    ->where('vehicle_classification.data_kb', config('params.data_kb.vehicle_classification_for_empty_car_info'));
            })->leftjoin('mst_general_purposes as empty_car_location', function ($join) {
                $join->on('empty_car_location.date_id', '=', 'empty_info.start_pref_cd')
                    ->where('empty_car_location.data_kb', config('params.data_kb.prefecture_cd'));
            });

    }
    public function store(Request $request, $id=null){
        $mEmptyInfo = new MEmptyInfo();
        $mBusinessOffices = new MBusinessOffices();
        $mGeneralPurposes = new MGeneralPurposes();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices();
        $listVehicleClassification= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_classification_for_empty_car_info'],'Empty');
        $listEquipment= $mGeneralPurposes->getInfoByDataKB(config('params.data_kb')['loaded_item']);
        $listPreferredPackage= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['preferred_package'],'');
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        return view('empty_info.form', [
            '$mEmptyInfo' => $mEmptyInfo,
            'listBusinessOffices' =>$listBusinessOffices,
            'listVehicleClassification' =>$listVehicleClassification,
            'listEquipment' =>$listEquipment,
            'listPreferredPackage' =>$listPreferredPackage,
            'listPrefecture' => $listPrefecture,
            'role' => 1,
        ]);
    }

    public function searchVehicle(Request $request){
        $input = $request->all();
        $mVehicle =  new MVehicles();
        $data =  $mVehicle
            ->select(
                'mst_vehicles.registration_numbers',
                'mst_vehicles.max_loading_capacity',
                'size.date_nm as vehicle_size_kb',
                'shape.date_nm as car_body_shape'
            )
            ->leftjoin(DB::raw('mst_general_purposes size'), function ($join) {
                $join->on('size.date_id', '=', 'mst_vehicles.vehicle_size_kb')
                    ->where('size.data_kb', config('params.data_kb.vehicle_size_kb'));
            })
             ->leftjoin(DB::raw('mst_general_purposes shape'), function ($join) {
                $join->on('shape.date_id', '=', 'mst_vehicles.car_body_shape_id')
                    ->where('shape.data_kb', config('params.data_kb.car_body_shape'));
            })
            ->where('mst_vehicles.deleted_at','=',null)
            ->where('mst_vehicles.mst_business_office_id','=',$input['mst_business_office_id'])
            ->where(function($q) use ($input) {
                $q->where('registration_numbers','LIKE','%'.$input['registration_numbers'].'%')->orWhere('registration_numbers','LIKE','%'.mb_convert_kana($input['registration_numbers'], "A", 'UTF-8').'%');
            })
            ->get();
        if(count($data) > 0){
            if(count($data) > 1){
                return response()->json([
                    'success'=>false,
                    'msg'=> Lang::get('messages.MSG10011'),
                ]);
            }
            return response()->json([
                'success'=>true,
                'info'=> $data[0],
            ]);
        }else{
            return response()->json([
                'success'=>false,
                'msg'=> Lang::get('messages.MSG10010'),
            ]);
        }
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
            'vehicle_size'=> [
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
                "classTH" => "wd-160",
                "classTD" => "text-center"
            ],
            'start_pref_cd'=> [
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
        $askingBaggages = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['preferred_package']);
        $startPrefCds = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb')['prefecture_cd']);
        $businessOffices = $mBussinessOffice->getAllData();
        return view('empty_info.index',[
                                    'fieldShowTable'=>$fieldShowTable,
                                    'businessOffices'=> $businessOffices,
                                    'askingBaggages'=>$askingBaggages,
                                    'startPrefCds'=>$startPrefCds]);
    }

}