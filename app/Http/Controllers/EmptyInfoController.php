<?php

namespace App\Http\Controllers;


use App\Helpers\TimeFunction;
use App\Http\Controllers\TraitRepositories\FormTrait;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MEmptyInfo;
use App\Models\MEmptyMailTo;
use App\Models\MGeneralPurposes;
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
        'max_load_capacity' => 'nullable|one_byte_number|length:5',
        'equipment' => 'required',
        'start_date' => 'required',
        'start_time' => 'required',
        'start_pref_cd' => 'required',
        'start_address' => 'required|length:200',
        'asking_price' => 'required|decimal_custom|length:11',
        'asking_baggage' => 'required',
        'arrive_pref_cd' => 'required',
        'arrive_address' => 'required|length:50',
        'arrive_date' => 'required',
    ];

    public $labels = [
        "regist_office_id" => "営業所",
        "vehicle_kb" => "車区分",
        "registration_numbers" => "自動車登録番号",
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
        if(isset( $data["id"])) {
            $this->ruleValid['equipment'] = 'required|length:200';
        }
    }
    public function store(Request $request, $id=null){
        $mEmptyInfo = null;
        if($id != null){
            $mEmptyInfo = MEmptyInfo::find( $id );
            if(empty($mEmptyInfo)){
                abort('404');
            }else{
                $mEmptyInfo->start_time = TimeFunction::convertTime24To12($mEmptyInfo->start_time);
                $mEmptyInfo = $mEmptyInfo->toArray();
            }
        }
        $mBusinessOffices = new MBusinessOffices();
        $mGeneralPurposes = new MGeneralPurposes();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices();
        $listVehicleClassification= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_classification_for_empty_car_info'],'Empty');
        $listEquipment= $mGeneralPurposes->getInfoByDataKB(config('params.data_kb')['loaded_item']);
        $listPreferredPackage= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['preferred_package'],'');
        $listPrefecture= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['prefecture_cd'],'');
        return view('empty_info.form', [
            'mEmptyInfo' => $mEmptyInfo,
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
        $arrayInsert['status'] = 1;
        $arrayInsert['regist_staff'] = Auth::user()->id;
        $empty_mail_add = MEmptyMailTo::where('office_id',Auth::user()->mst_business_office_id)->first();
        $arrayInsert['email_address'] = $empty_mail_add ?$empty_mail_add->email_address : null;
        $arrayInsert['start_time'] = TimeFunction::parseStringToTime($arrayInsert['start_time']);
        unset($arrayInsert["id"]);
        DB::beginTransaction();
        if(isset( $data["id"]) && $data["id"]){
            $id = $data["id"];
            $arrayInsert["modified_at"] = $currentTime;
            MEmptyInfo::query()->where("id","=",$id)->update( $arrayInsert );
        }else {
            $arrayInsert["created_at"] = $currentTime;
            $arrayInsert["modified_at"] = $currentTime;
            $id =  MEmptyInfo::query()->insertGetId( $arrayInsert );
        }
        DB::commit();
        if(isset( $data["id"])){
            $this->backHistory();
        }
        \Session::flash('message',Lang::get('messages.MSG03002'));
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

    public function checkIsExist($id){
        $mEmptyInfo = new MEmptyInfo();
        $mEmptyInfo = $mEmptyInfo->find($id);
        if (isset($mEmptyInfo)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
        }
    }
}