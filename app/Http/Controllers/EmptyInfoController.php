<?php

namespace App\Http\Controllers;


use App\Helpers\TimeFunction;
use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MEmptyInfo;
use App\Models\MEmptyMailTo;
use App\Models\MGeneralPurposes;
use App\Models\MStaffs;
use App\Models\MVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class EmptyInfoController extends Controller {
    use ListTrait,FormTrait;
    public $table = "empty_info";
    public $allNullAble = false;
    public $beforeItem = null;

    public $ruleValid = [
        'regist_office_id' => 'required',
        'vehicle_kb' => 'required',
        'registration_numbers' => 'nullable|length:50',
        'vehicle_size' => 'required|length:50',
        'vehicle_body_shape' => 'required|length:50',
        'max_load_capacity' => 'required|one_byte_number|length:5',
        'equipment' => 'required',
        'start_date' => 'required',
        'start_time' => 'required',
        'start_pref_cd' => 'required',
        'start_address' => 'required|length:200',
        'asking_price' => 'required|one_byte_number|length:8',
        'asking_baggage' => 'required',
        'arrive_pref_cd' => 'required',
        'arrive_address' => 'required|length:50',
        'arrive_date' => 'required',
    ];

    public $labels = [
        "status" => "ステータス",
        "regist_office_id" => "営業所",
        "vehicle_kb" => "車両区分",
        "registration_numbers" => "車番",
        "vehicle_size" => "車格",
        "vehicle_body_shape" => "形状",
        "max_load_capacity" => "最大積載量",
        "equipment" => "搭載物",
        "start_date" => "空車予定日",
        "start_time" => "空車予定時間",
        "start_pref_cd" => "空車予定都道府県",
        "start_address" => "空車予定住所",
        "asking_price" => "希望運賃",
        "asking_baggage" => "希望荷物",
        "arrive_pref_cd" => "到着予定都道府県",
        "arrive_address" => "到着予定住所",
        "arrive_date" => "到着日",
    ];

    public $messagesCustom = [];

    public function __construct(){
//        $this->labels = Lang::get("empty_info.create.field");
        parent::__construct();
    }
    public function beforeSubmit($data){
        if($data['mode']=='edit'){
            $this->ruleValid['status'] = 'required';
        }
        if(isset( $data["id"])) {
            $this->ruleValid['equipment'] = 'required|length:200';
        }
    }
    protected function search($data){
        $currentDate = date("Y-m-d",time());
        $dataSearch=$data['fieldSearch'];
        $this->query->select('mst_business_offices.business_office_nm as regist_office',
               'empty_info.id',
               'empty_info.regist_office_id',
               'vehicle_classification.date_nm as vehicle_classification',
               'empty_info.registration_numbers',
               'empty_info.vehicle_size',
               'empty_info.vehicle_body_shape',
               'empty_info.max_load_capacity',
               'empty_info.equipment',
               'pref_asking_baggage.date_nm as asking_baggage',
                'empty_info.status',
                DB::raw('format(empty_info.asking_price, "#,##0") as asking_price'),
                DB::raw("CONCAT_WS(' ',DATE_FORMAT(empty_info.start_date, '%Y/%m/%d'),TIME_FORMAT(empty_info.start_time,'%H:%i')) as schedule_date"),
                DB::raw("CONCAT_WS(' ',empty_car_location.date_nm, empty_info.start_address) as start_pref_cd"),
                DB::raw("CONCAT_WS(' ',arrive_location.date_nm, empty_info.arrive_address) as arrive_location"),
                DB::raw("DATE_FORMAT(empty_info.arrive_date, '%Y/%m/%d') as arrive_date")
            );
            $this->query->leftJoin('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'empty_info.regist_office_id');
            })->leftjoin('mst_general_purposes as vehicle_classification', function ($join) {
                $join->on('vehicle_classification.date_id', '=', 'empty_info.vehicle_kb')
                    ->where('vehicle_classification.data_kb', config('params.data_kb.vehicle_classification_for_empty_car_info'));
            })->leftjoin('mst_general_purposes as empty_car_location', function ($join) {
                $join->on('empty_car_location.date_id', '=', 'empty_info.start_pref_cd')
                    ->where('empty_car_location.data_kb', config('params.data_kb.prefecture_cd'));
            })->leftjoin('mst_general_purposes as arrive_location', function ($join) {
                $join->on('arrive_location.date_id', '=', 'empty_info.arrive_pref_cd')
                    ->where('arrive_location.data_kb',config('params.data_kb.prefecture_cd'));
            })->leftjoin('mst_general_purposes as pref_asking_baggage', function ($join) {
                $join->on('pref_asking_baggage.date_id', '=', 'empty_info.asking_baggage')
                    ->where('pref_asking_baggage.data_kb',config('params.data_kb.preferred_package'));
            });
            if ($dataSearch['regist_office_id'] != '') {
                $this->query->where('empty_info.regist_office_id', '=', $dataSearch['regist_office_id'] );
            };
            if ($dataSearch['start_pref_cd'] != '') {
                $this->query->where('empty_info.start_pref_cd', '=',  $dataSearch['start_pref_cd']);
            }
            if ($dataSearch['start_address'] != '') {
                $this->query->where('empty_info.start_address', 'LIKE', '%' . $dataSearch['start_address'] . '%');
            }
            if ($dataSearch['arrive_pref_cd'] != '') {
                $this->query->where('empty_info.arrive_pref_cd', '=',  $dataSearch['arrive_pref_cd']);
            }
            if ($dataSearch['arrive_address'] != '') {
                $this->query->where('empty_info.arrive_address', 'LIKE', '%' . $dataSearch['arrive_address'] . '%');
            }
            if ($dataSearch['vehicle_size'] != '') {
                $this->query->where('empty_info.vehicle_size', 'LIKE', '%' . $dataSearch['vehicle_size'] . '%');
            }
            if ($dataSearch['vehicle_body_shape'] != '') {
                $this->query->where('empty_info.vehicle_body_shape', 'LIKE', '%' . $dataSearch['vehicle_body_shape'] . '%');
            }
            if ($dataSearch['asking_baggage'] != '') {
                $this->query->where('empty_info.asking_baggage', 'LIKE', '%' . $dataSearch['asking_baggage'] . '%');
            }
            if ($dataSearch['equipment'] != '') {
                $this->query->where('empty_info.equipment', 'LIKE', '%' . $dataSearch['equipment'] . '%');
            }
            if(!$dataSearch['status'] || $dataSearch['status']==false)
            {
                $this->query->where(function ($query) {
                        $query->where('empty_info.status', 1)
                            ->orWhere('empty_info.status', 2);
                });
            }
            if(!$dataSearch['arrive_date'] || $dataSearch['arrive_date']==false)
            {
                $this->query->where('empty_info.arrive_date','>=',$currentDate);
            }
            $this->query->where('empty_info.deleted_at',null);
            if ($data["order"]["col"] != '') {
                if ($data["order"]["col"] == 'arrive_location')
                    $orderCol = 'CONCAT_WS("    ",arrive_location.date_nm_kana, empty_info.arrive_address)';
                else if($data["order"]["col"]=='regist_office')
                    $orderCol='regist_office_id';
                else if($data["order"]["col"]=='vehicle_classification')
                    $orderCol='vehicle_kb';
                else if($data["order"]["col"]=='asking_baggage')
                    $orderCol='empty_info.asking_baggage';
                else if($data["order"]["col"]=='schedule_date')
                    $orderCol="CONCAT_WS(' ',DATE_FORMAT(empty_info.start_date, '%Y/%m/%d'),TIME_FORMAT(empty_info.start_time,'%H:%i'))";
                else if($data["order"]["col"]=='start_pref_cd')
                    $orderCol="CONCAT_WS(' ',empty_car_location.date_nm_kana, empty_info.start_address)";
                else
                    $orderCol = $data["order"]["col"];
                if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                    $orderCol .= " DESC";
                }
                $this->query->orderbyRaw($orderCol);
            } else {
                $this->query->orderBy('empty_info.arrive_date','asc')
                    ->orderBy('empty_info.id','desc');
            }
    }

    public function index(Request $request){
        $fieldShowTable = [
            'regist_office' => [
                "classTH" => "wd-100",
                "sortBy"=>"regist_office"
            ],
            'vehicle_classification'=> [
                "classTH" => "wd-60",
                "sortBy"=>"vehicle_classification"
            ],
            'registration_numbers'=> [
                "classTH" => "wd-120",
                "sortBy"=>"registration_numbers"
            ],
            'vehicle_size'=> [
                "classTH" => "wd-60",
                "classTD" => "td-nl2br",
                "sortBy"=>"vehicle_size"
            ],
            'vehicle_body_shape'=> [
                "classTH" => "wd-120",
                "sortBy"=>"vehicle_body_shape"
            ],
            'max_load_capacity'=> [
                "classTH" => "wd-100",
                "sortBy"=>"max_load_capacity"
            ],
            'equipment'=> [
                "classTH" => "wd-120",
                "classTD" => "td-nl2br ",
                "sortBy"=>"equipment"
            ],
            'schedule_date'=> [
                "classTH" => "wd-120",
                "sortBy"=>"schedule_date"
            ],
            'start_pref_cd'=> [
                "classTH" => "wd-120",
                "sortBy"=>"start_pref_cd"
            ],
            'asking_price'=> [
                "classTH" => "wd-100",
                "sortBy"=>"asking_price"
            ],
            'asking_baggage'=> [
                "classTH" => "wd-100",
                "sortBy"=>"asking_baggage"
            ],
            'arrive_location'=> [
                "classTH" => "wd-120",
                "sortBy"=>"arrive_location",
            ],
            'arrive_date'=> [
                "classTH" => "wd-120",
                "sortBy"=>"arrive_date",
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


    public function store(Request $request, $id=null){
        $mEmptyInfo = null;
        $mode = "register";
        $role = 1;
        if($id != null){
            $mEmptyInfo = MEmptyInfo::find( $id );
            if(empty($mEmptyInfo)){
                abort('404');
            }else{
                $mEmptyInfo = $mEmptyInfo->toArray();
                $routeName = $request->route()->getName();
                switch ($routeName){
                    case 'empty_info.reservation':
                        $mode = 'reservation';
                        if(($mEmptyInfo['status']==1 || $mEmptyInfo['status']==2 ) && $mEmptyInfo['regist_office_id']== Auth::user()->mst_business_office_id ){
                            $role = 2; // no authentication
                        }
                        break;
                    case 'empty_info.reservation_approval':
                        $ask_staff= MStaffs::query()->select(DB::raw("concat(last_nm,'　',first_nm) as ask_staff"))->where('staff_cd' ,$mEmptyInfo['ask_staff'])->first();
                        if($ask_staff){
                            $mEmptyInfo['reservation_person'] = $ask_staff->ask_staff;
                        }
                        $mode = 'reservation_approval';
                        if($mEmptyInfo['status']!=2 || $mEmptyInfo['regist_office_id']!= Auth::user()->mst_business_office_id ){
                            $role = 2; // no authentication
                        }
                        break;
                    default:
                        $mode ='edit';
                        if($mEmptyInfo['status']!=1 || $mEmptyInfo['regist_office_id']!= Auth::user()->mst_business_office_id ){
                            $role = 2; // no authentication
                        }
                        break;
                }
            }
        }
        $mBusinessOffices = new MBusinessOffices();
        $mGeneralPurposes = new MGeneralPurposes();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices();
        $listVehicleClassification= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_classification_for_empty_car_info'],'Empty');
        $listEquipment= $mGeneralPurposes->getInfoByDataKB(config('params.data_kb')['loaded_item']);
        $listPreferredPackage= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['preferred_package'],'');
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        $listStatus= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['empty_car_info_status'],'');
        return view('empty_info.form', [
            'mEmptyInfo' => $mEmptyInfo,
            'listBusinessOffices' =>$listBusinessOffices,
            'listVehicleClassification' =>$listVehicleClassification,
            'listEquipment' =>$listEquipment,
            'listPreferredPackage' =>$listPreferredPackage,
            'listPrefecture' => $listPrefecture,
            'listStatus' => $listStatus,
            'role' => $role,
            'mode' => $mode
        ]);
    }

    public function searchVehicle(Request $request){
        $input = $request->all();
        $input['registration_numbers'] = str_pad($input['registration_numbers'], 4, '0', STR_PAD_LEFT);
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

    protected function validAfter( &$validator,$data ){
        if($data['asking_price']!= ""  && is_numeric($data['asking_price']) && $data['asking_price'] < 1){
            $validator->errors()
                ->add("asking_price", str_replace(':attribute',$this->labels['asking_price'],Lang::get('messages.MSG02023')));
        }
        if(!isset( $data["id"])) {
            $equipment = $data["equipment"];
            $errorsEx = [];
            $mGeneralPurposes = new MGeneralPurposes();
            $listEquipment = $mGeneralPurposes->getInfoByDataKB(config('params.data_kb')['loaded_item']);
            $listEquipment = $listEquipment->groupBy('date_id')->toArray();
            if (count($equipment) > 0) {
                foreach ($equipment as $index => $items) {
                    if ($items['id'] == 0) {
                        if ($items['value'] == '') {
                            $errorsEx[$items['id']] = str_replace(':attribute', 'その他', Lang::get('validation.required'));
                        }
                        continue;
                    };
                    $validatorEx = Validator::make($items, [
                        'value' => 'required|one_byte_number',
                    ], $this->messagesCustom, ['value' => $listEquipment[$items['id']][0]->date_nm]);
                    if ($validatorEx->fails()) {
                        $errorsEx[$items['id']] = $validatorEx->errors()->get('value')[0];
                    }
                }
            }
            if (count($errorsEx) > 0) {
                $validator->errors()
                    ->add("equipment_value", $errorsEx);
            } else {
                $equipmentStr = '';
                foreach ($equipment as $index => $items) {
                    if ($items['id'] == 0) {
                        $equipmentStr .= 'その他 ' . $items['value'] . "\n";
                    } else {
                        $equipmentStr .= $listEquipment[$items['id']][0]->date_nm . ' ' . $items['value'] . $listEquipment[$items['id']][0]->contents1 . "\n";
                    }
                }
                $validatorEx = Validator::make(['equipment' => $equipmentStr], ['equipment' => 'nullable|length:200'], $this->messagesCustom, $this->labels);
                if ($validatorEx->fails()) {
                    $validator->errors()
                        ->add("equipment", $validatorEx->errors()->first());
                } else {
                    $data['equipment'] = $equipmentStr;
                }
            }
        }
    }

    protected function save($data){
        $arrayInsert = $data;
        $currentTime = date("Y-m-d H:i:s",time());
        $equipment =  $data["equipment"];
        $equipmentStr = '';
        if(!isset( $data["id"])) {
            usort($equipment, function($a, $b) {
                if ($a['id'] == $b['id']) return 0;
                if ($a['id'] == 0) return 1;
                if ($b['id'] == 0) return -1;
                return $a['id'] > $b['id'] ? 1 : -1;
            });
            $mGeneralPurposes = new MGeneralPurposes();
            $listEquipment = $mGeneralPurposes->getInfoByDataKB(config('params.data_kb')['loaded_item']);
            $listEquipment = $listEquipment->groupBy('date_id')->toArray();
            foreach ($equipment as $index => $items) {
                if ($items['id'] == 0) {
                    $equipmentStr .= 'その他 ' . $items['value'] . ($index==count($equipment)-1 ? '' :"\n");
                } else {
                    $equipmentStr .= $listEquipment[$items['id']][0]->date_nm . ' ' . $items['value'] . $listEquipment[$items['id']][0]->contents1 . ($index==count($equipment)-1 ? '' :"\n");
                }
            }
            $arrayInsert['equipment'] = $equipmentStr;

        }
        $arrayInsert['regist_staff'] = Auth::user()->staff_cd;
        $empty_mail_add = MEmptyMailTo::where('office_id',Auth::user()->mst_business_office_id)->first();
        $arrayInsert['email_address'] = $empty_mail_add ?$empty_mail_add->email_address : null;
        $arrayInsert['start_time'] = TimeFunction::parseStringToTime($arrayInsert['start_time']);
        unset($arrayInsert["id"]);
        unset($arrayInsert["mode"]);
        DB::beginTransaction();
        if(isset( $data["id"]) && $data["id"]){
            $id = $data["id"];
            $arrayInsert["modified_at"] = $currentTime;
            MEmptyInfo::query()->where("id","=",$id)->update( $arrayInsert );
            MEmptyInfo::updateStatus($id, $arrayInsert['status']);
        }else {
            $arrayInsert['status'] = 1;
            $arrayInsert["created_at"] = $currentTime;
            $arrayInsert["modified_at"] = $currentTime;
            $id =  MEmptyInfo::query()->insertGetId( $arrayInsert );
        }
        DB::commit();
        if(isset( $data["id"])){
            $this->backHistory();
            \Session::flash('message',Lang::get('messages.MSG04002'));
        }else{
            \Session::flash('message',Lang::get('messages.MSG03002'));
        }
        return $id;
    }

    public function delete($id)
    {
        $mEmptyInfo = MEmptyInfo::find($id);
        $this->backHistory();
        if ($mEmptyInfo->delete()) {
            \Session::flash('message',Lang::get('messages.MSG10004'));
            $response = ['data' => 'success'];
        } else {
            \Session::flash('message',Lang::get('messages.MSG06002'));
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExist(Request $request, $id){
        $status= $request->get('status');
        $mode = $request->get('mode');
        $mEmptyInfo = new MEmptyInfo();
        $mEmptyInfo = $mEmptyInfo->find($id);
        if (isset($mEmptyInfo)) {
            return Response()->json(array('success'=>true));
        } else {
            if($mode=='edit'){
                $message = Lang::get('messages.MSG04001');
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
        }
    }

    public function updateStatus(Request $request, $id){
        $result = MEmptyInfo::updateStatus($id, $request->get('status'));
        $this->backHistory();
        switch ($request->get('status')){
            case 1:
                \Session::flash('message',Lang::get('messages.'.($result ? 'MSG10020' : 'MSG10021')));
                break;
            case 2:
                \Session::flash('message',Lang::get('messages.'.($result ? 'MSG10014' : 'MSG10015')));
                break;
            case 8:
                \Session::flash('message',Lang::get('messages.'.($result ? 'MSG10017' : 'MSG10018')));
                break;
        }
        return response()->json([
            'success'=>true,
            'message'=> [],
        ]);
    }
}
