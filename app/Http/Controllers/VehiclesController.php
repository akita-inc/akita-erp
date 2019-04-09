<?php

namespace App\Http\Controllers;

use App\Helpers\Common;
use App\Helpers\TimeFunction;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MModifyLogs;
use App\Models\MStaffs;
use App\Models\MVehicles;
use App\Models\MStaffAuths;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TraitRepositories\FormTrait;

class VehiclesController extends Controller
{

    use ListTrait,FormTrait;
    public $table = "mst_vehicles";

    public function __construct(){
        parent::__construct();
    }

    public function index(Request $request)
    {
        $mGeneralPurpose = new MGeneralPurposes();
        $mBussinessOffice = new MBusinessOffices();
        $vehicleKbs = $mGeneralPurpose->getDataByMngDiv(config('params.data_kb.vehicles_kb'));

        return view('vehicles.index', [
            'vehicle_kbs' => $vehicleKbs,
            'business_offices' => $mBussinessOffice->get(),
        ]);
    }

    protected function search($data){
        $where = array(
            'vehicles_cd' => $data['fieldSearch']['vehicles_cd'],
            'door_number' => $data['fieldSearch']['door_number'],
            'vehicles_kb' => $data['fieldSearch']['vehicles_kb'],
            'registration_numbers' => $data['fieldSearch']['registration_numbers'],
            'mst_business_office_id' => $data['fieldSearch']['mst_business_office_id'],
        );

        $this->query->select('mst_vehicles.id',
            'mst_vehicles.vehicles_cd',
            'mst_vehicles.door_number',
            'mst_general_purposes.date_nm AS vehicles_kb_nm',
            'mst_vehicles.registration_numbers',
            'mst_business_offices.business_office_nm',
            'size.date_nm AS vehicle_size_kb_nm',
            'purpose.date_nm AS vehicle_purpose_nm',
            DB::raw("DATE_FORMAT(mst_vehicles.modified_at, '%Y/%m/%d') as modified_at")
            );

        $this->query
            ->leftjoin('mst_general_purposes', function ($join) {
                $join->on('mst_general_purposes.date_id', '=', 'mst_vehicles.vehicles_kb')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb.vehicles_kb'));
            })
            ->leftjoin('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'mst_vehicles.mst_business_office_id');
            })
            ->leftjoin(DB::raw('mst_general_purposes size'), function ($join) {
                $join->on('size.date_id', '=', 'mst_vehicles.vehicle_size_kb')
                    ->where('size.data_kb', config('params.data_kb.vehicle_size_kb'));
            })
            ->leftjoin(DB::raw('mst_general_purposes purpose'), function ($join) {
                $join->on('purpose.date_id', '=', 'mst_vehicles.vehicle_purpose_id')
                    ->where('purpose.data_kb', config('params.data_kb.vehicle_purpose'));
            })
            ->whereRaw('mst_vehicles.deleted_at IS NULL');

        if ($where['vehicles_cd'] != '') {
            $this->query->where('mst_vehicles.vehicles_cd', "LIKE", "%{$where['vehicles_cd']}%");
        }
        if ($where['door_number'] != '') {
            $this->query->where('mst_vehicles.door_number', "LIKE", "%{$where['door_number']}%");
        }
        if ($where['vehicles_kb'] != '') {
            $this->query->where('mst_vehicles.vehicles_kb', "=", $where['vehicles_kb']);
        }
        if ($where['registration_numbers'] != '') {
            $this->query->where('mst_vehicles.registration_numbers', "LIKE", "%{$where['registration_numbers']}%");
        }
        if ($where['mst_business_office_id'] != '') {
            $this->query->where('mst_vehicles.mst_business_office_id', "=", $where['mst_business_office_id']);
        }

        if ($data["order"]["col"] != '') {
            $orderCol = $data["order"]["col"];
            if (isset($data["order"]["descFlg"]) && $data["order"]["descFlg"]) {
                $orderCol .= " DESC";
            }
            $this->query->orderbyRaw($orderCol);
        } else {
            $this->query->orderby('mst_vehicles.vehicles_cd');
        }
    }

    public function delete(Request $request,$id)
    {
        $mVehicle = new MVehicles();
        $this->backHistory();
        if ($mVehicle->deleteVehicle($id)) {
            \Session::flash('message',Lang::get('messages.MSG10004'));
            $response = ['data' => 'success'];
        } else {
            \Session::flash('message',Lang::get('messages.MSG06002'));
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExist(Request $request,$id){
        $mode = $request->get('mode');
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->find($id);
        if (isset($mVehicle)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> is_null($mode) ? Lang::trans('messages.MSG06003') : Lang::trans('messages.MSG04001')));
        }
    }

    public function create(Request $request, $id=null){
        $mVehicle = new MVehicles();
        $mGeneralPurposes = new MGeneralPurposes();
        $mBusinessOffices = new MBusinessOffices();
        $listBusinessOffices = $mBusinessOffices->getListBusinessOffices();
        $listVehicleKb= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicles_kb'],'');
        $listVehicleSize= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_size_kb'],'');
        $listVehiclePurpose= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_purpose'],'');
        $listLandTranportOfficeCd= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['land_transport_office_cd'],'');
        $listVehicleClassification= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle_classification'],'');
        $listPrivateCommercial= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['private_commercial'],'');
        $listCarBodyShape= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['car_body_shape'],'');
        $listVehicle= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['vehicle'],'');
        $listKindOfFuel= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['kinds_of_fuel'],'');
        $listDriveSystem= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['drive_system'],'');
        $listTransmissions= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['transmissions'],'');
        $listSuspensionsCd= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['suspensions_cd'],'');
        $listPowerGate= $mGeneralPurposes->getDateIDByDataKB(config('params.data_kb')['power_gate_cd'],'');
        $mStaffAuth =  new MStaffAuths();
        $role = $mStaffAuth->getRoleBySCreen(4);

        if(!is_null($id)){
            $mVehicle = $mVehicle->find($id);
            if(is_null($mVehicle)){
                return abort(404);
            }
        }
        

        return view('vehicles.create',[
            'mVehicle' => $mVehicle->toArray(),
            'listBusinessOffices' => $listBusinessOffices,
            'listVehicleKb' => $listVehicleKb,
            'listVehicleSize' => $listVehicleSize,
            'listVehiclePurpose' => $listVehiclePurpose,
            'listLandTranportOfficeCd' => $listLandTranportOfficeCd,
            'listVehicleClassification' => $listVehicleClassification,
            'listPrivateCommercial' => $listPrivateCommercial,
            'listCarBodyShape' => $listCarBodyShape,
            'listVehicle' => $listVehicle,
            'listKindOfFuel' => $listKindOfFuel,
            'listDriveSystem' => $listDriveSystem,
            'listTransmissions' => $listTransmissions,
            'listSuspensionsCd' => $listSuspensionsCd,
            'listPowerGate' => $listPowerGate,
            'role' => $role
        ]);
    }

    public function save(Request $request){
        $mVehicle = new MVehicles();
        $input = $request->all();
        $data = json_decode($input['data'], true);
        $data['vehicle_inspection_sticker_pdf'] = $input['vehicle_inspection_sticker_pdf'];
        $data['picture_fronts'] = $input['picture_fronts'];
        $data['picture_rights'] = $input['picture_rights'];
        $data['picture_lefts'] = $input['picture_lefts'];
        $data['picture_rears'] = $input['picture_rears'];
        $data['registration_dt'] = TimeFunction::dateFormat($data["registration_dt"],'Y-m-d');
        $data['expiry_dt'] = TimeFunction::dateFormat($data["expiry_dt"],'Y-m-d');
        $data['vehicle_delivery_dt'] = TimeFunction::dateFormat($data["vehicle_delivery_dt"],'Y-m-d');
        $data['dispose_dt'] = TimeFunction::dateFormat($data["dispose_dt"],'Y-m-d');
        array_walk_recursive($data, function (& $item, $key) {if ($item=='') { $item = null; }});
        $mode = $data['mode'];

        $id = isset($data['id']) ?$data['id']  :null;
        if(!is_null($id)){
            $mVehicle = $mVehicle->find($id);
            $dataBeforeUpdate = $mVehicle->toArray();
        }
        $rules = [
            'vehicles_cd'=>'required|one_byte_number|length:10|number_range|unique:mst_vehicles,vehicles_cd,NULL,id,deleted_at,NULL',
            'door_number'=>'required|one_byte_number|length:10|number_range',
            'registration_numbers'=>'required|length:50',
            'mst_business_office_id'=>'required',
            'vehicle_inspection_sticker_pdf'=>'nullable|mimes:pdf|max_mb:'.config('params.max_file_size'),
            'first_year_registration_dt'=>'nullable|date_format_custom:Ym',
            'seating_capacity'=>'nullable|one_byte_number|length:2',
            'max_loading_capacity'=>'nullable|one_byte_number|length:5',
            'vehicle_body_weights'=>'nullable|one_byte_number|length:5',
            'vehicle_total_weights'=>'nullable|one_byte_number|length:5',
            'frame_numbers'=>'nullable|length:10',
            'vehicle_lengths'=>'nullable|one_byte_number|length:4',
            'vehicle_widths'=>'nullable|one_byte_number|length:3',
            'vehicle_heights'=>'nullable|one_byte_number|length:3',
            'axle_loads_ff'=>'nullable|one_byte_number|length:5',
            'axle_loads_fr'=>'nullable|one_byte_number|length:5',
            'axle_loads_rf'=>'nullable|one_byte_number|length:5',
            'axle_loads_rr'=>'nullable|one_byte_number|length:5',
            'vehicle_types'=>'nullable|length:50',
            'engine_typese'=>'nullable|length:50',
            'total_displacements'=>'nullable|one_byte_number|length:5',
            'rated_outputs'=>'nullable|one_byte_number|length:5',
            'type_designation_numbers'=>'nullable|length:50',
            'id_segment_numbers'=>'nullable|length:50',
            'owner_nm'=>'nullable|length:50',
            'owner_address'=>'nullable|length:200',
            'user_nm'=>'nullable|length:50',
            'user_address'=>'nullable|length:200',
            'user_base_locations'=>'nullable|length:200',
            'car_inspections_notes'=>'nullable|length:50',
            'digital_tachograph_numbers'=>'nullable|one_byte_number|length:10|number_range',
            'etc_numbers'=>'nullable|length:19',
            'drive_recorder_numbers'=>'nullable|length:10',
            'transmissions_notes'=>'nullable|length:50',
            'tank_capacity_1'=>'nullable|one_byte_number|length:3',
            'tank_capacity_2'=>'nullable|one_byte_number|length:3',
            'loading_inside_dimension_capacity_length'=>'nullable|one_byte_number|length:5',
            'loading_inside_dimension_capacity_width'=>'nullable|one_byte_number|length:5',
            'loading_inside_dimension_capacity_height'=>'nullable|one_byte_number|length:5',
            'specification_notes'=>'nullable|length:200',
            'personal_insurance_prices'=>'nullable|decimal_custom|length:11',
            'property_damage_insurance_prices'=>'nullable|decimal_custom|length:11',
            'vehicle_insurance_prices'=>'nullable|decimal_custom|length:11',
            'picture_fronts'=>'nullable|mimes:jpeg,jpg,png|max_mb:'.config('params.max_file_size'),
            'picture_rights'=>'nullable|mimes:jpeg,jpg,png|max_mb:'.config('params.max_file_size'),
            'picture_lefts'=>'nullable|mimes:jpeg,jpg,png|max_mb:'.config('params.max_file_size'),
            'picture_rears'=>'nullable|mimes:jpeg,jpg,png|max_mb:'.config('params.max_file_size'),
            'acquisition_amounts'=>'nullable|decimal_custom|length:11',
            'acquisition_amortization'=>'nullable|one_byte_number|length:3',
            'durable_years'=>'nullable|one_byte_number|length:3',
            'tire_sizes'=>'nullable|length:10',
            'battery_sizes'=>'nullable|length:10',
            'notes'=>'nullable|length:100',
        ];
        if($id!= null){
            $rules['vehicles_cd'] = "required|one_byte_number|length:10|number_range|unique:mst_vehicles,vehicles_cd,{$id},id,deleted_at,NULL";
        }
        $customMessages = [
            'vehicle_inspection_sticker_pdf.mimes' => Lang::get('messages.MSG02017'),
            'picture_fronts.mimes' => Lang::get('messages.MSG02018'),
            'picture_rights.mimes' => Lang::get('messages.MSG02018'),
            'picture_lefts.mimes' => Lang::get('messages.MSG02018'),
            'picture_rears.mimes' => Lang::get('messages.MSG02018'),
            'vehicles_cd.unique' => str_replace(':screen','車両',Lang::get('messages.MSG10003'))
        ];

        $validator = Validator::make($data, $rules,$customMessages,$mVehicle->label);

        if ($validator->fails()) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else{
            DB::beginTransaction();
            try {
                $mVehicle->vehicles_cd = $data["vehicles_cd"];
                $mVehicle->door_number = $data["door_number"];
                $mVehicle->vehicles_kb = $data["vehicles_kb"];
                $mVehicle->registration_numbers = $data["registration_numbers"];
                $mVehicle->mst_business_office_id = $data["mst_business_office_id"];
                $mVehicle->vehicle_size_kb = $data["vehicle_size_kb"];
                $mVehicle->vehicle_purpose_id = $data["vehicle_purpose_id"];
                $mVehicle->land_transport_office_cd = $data["land_transport_office_cd"];
                $mVehicle->registration_dt = $data["registration_dt"];
                $mVehicle->first_year_registration_dt = $data["first_year_registration_dt"];
                $mVehicle->vehicle_classification_id = $data["vehicle_classification_id"];
                $mVehicle->private_commercial_id = $data["private_commercial_id"];
                $mVehicle->car_body_shape_id = $data["car_body_shape_id"];
                $mVehicle->vehicle_id = $data["vehicle_id"];
                $mVehicle->seating_capacity = $data["seating_capacity"];
                $mVehicle->max_loading_capacity = $data["max_loading_capacity"];
                $mVehicle->vehicle_body_weights = $data["vehicle_body_weights"];
                $mVehicle->vehicle_total_weights = $data["vehicle_total_weights"];
                $mVehicle->frame_numbers = $data["frame_numbers"];
                $mVehicle->vehicle_lengths = $data["vehicle_lengths"];
                $mVehicle->vehicle_widths = $data["vehicle_widths"];
                $mVehicle->vehicle_heights = $data["vehicle_heights"];
                $mVehicle->axle_loads_ff = $data["axle_loads_ff"];
                $mVehicle->axle_loads_fr = $data["axle_loads_fr"];
                $mVehicle->axle_loads_rf = $data["axle_loads_rf"];
                $mVehicle->axle_loads_rr = $data["axle_loads_rr"];
                $mVehicle->vehicle_types = $data["vehicle_types"];
                $mVehicle->engine_typese = $data["engine_typese"];
                $mVehicle->total_displacements = $data["total_displacements"];
                $mVehicle->rated_outputs = $data["rated_outputs"];
                $mVehicle->kinds_of_fuel_id = $data["kinds_of_fuel_id"];
                $mVehicle->type_designation_numbers = $data["type_designation_numbers"];
                $mVehicle->id_segment_numbers = $data["id_segment_numbers"];
                $mVehicle->wireless_installation_fg = isset($data["wireless_installation_fg"]) ? 1 : 0;
                $mVehicle->owner_nm = $data["owner_nm"];
                $mVehicle->owner_address = $data["owner_address"];
                $mVehicle->user_nm = $data["user_nm"];
                $mVehicle->user_address = $data["user_address"];
                $mVehicle->user_base_locations = $data["user_base_locations"];
                $mVehicle->expiry_dt = $data["expiry_dt"];
                $mVehicle->car_inspections_notes = $data["car_inspections_notes"];
                $mVehicle->digital_tachograph_numbers = $data["digital_tachograph_numbers"];
                $mVehicle->etc_numbers = $data["etc_numbers"];
                $mVehicle->drive_recorder_numbers = $data["drive_recorder_numbers"];
                $mVehicle->bed_fg = isset($data["bed_fg"]) ? 1 : 0;
                $mVehicle->refrigerator_fg = isset($data["refrigerator_fg"]) ? 1 : 0;
                $mVehicle->drive_system_id = $data["drive_system_id"];
                $mVehicle->transmissions_id = $data["transmissions_id"];
                $mVehicle->transmissions_notes = $data["transmissions_notes"];
                $mVehicle->suspensions_cd = $data["suspensions_cd"];
                $mVehicle->tank_capacity_1 = $data["tank_capacity_1"];
                $mVehicle->tank_capacity_2 = $data["tank_capacity_2"];
                $mVehicle->loading_inside_dimension_capacity_length = $data["loading_inside_dimension_capacity_length"];
                $mVehicle->loading_inside_dimension_capacity_width = $data["loading_inside_dimension_capacity_width"];
                $mVehicle->loading_inside_dimension_capacity_height = $data["loading_inside_dimension_capacity_height"];
                $mVehicle->snowmelt_fg = isset($data["snowmelt_fg"]) ? 1 : 0;
                $mVehicle->double_door_fg = isset($data["double_door_fg"]) ? 1 : 0;
                $mVehicle->floor_iron_plate_fg = isset($data["floor_iron_plate_fg"]) ? 1 : 0;
                $mVehicle->floor_sagawa_embedded_fg = isset($data["floor_sagawa_embedded_fg"]) ? 1 : 0;
                $mVehicle->floor_roller_fg = isset($data["floor_roller_fg"]) ? 1 : 0;
                $mVehicle->floor_joloda_conveyor_fg = isset($data["floor_joloda_conveyor_fg"]) ? 1 : 0;
                $mVehicle->power_gate_cd = $data["power_gate_cd"];
                $mVehicle->vehicle_delivery_dt = $data["vehicle_delivery_dt"];
                $mVehicle->specification_notes = $data["specification_notes"];
                $mVehicle->mst_staff_cd = $data["mst_staff_cd"];
                $mVehicle->personal_insurance_prices = $data["personal_insurance_prices"];
                $mVehicle->property_damage_insurance_prices = $data["property_damage_insurance_prices"];
                $mVehicle->vehicle_insurance_prices = $data["vehicle_insurance_prices"];
                $mVehicle->acquisition_amounts = $data["acquisition_amounts"];
                $mVehicle->acquisition_amortization = $data["acquisition_amortization"];
                $mVehicle->durable_years = $data["durable_years"];
                $mVehicle->tire_sizes = $data["tire_sizes"];
                $mVehicle->battery_sizes = $data["battery_sizes"];
                $mVehicle->dispose_dt = $data["dispose_dt"];
                $mVehicle->notes = $data["notes"];

                $mVehicle->save();

                //deleteFile
                if (isset($data['deleteFile']) && count($data['deleteFile']) > 0) {
                    foreach ($data['deleteFile'] as $item) {
                        $mVehicle->{$item} = null;
                    }
                }
                $mVehicle->save();

                //upload file
                $directoryPath = config('params.vehicles_path') . $mVehicle->id;
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0777, true);
                }
                foreach ($request->allFiles() as $key => $item) {
                    $file = $data[$key];
                    $mVehicle->{$key} = Common::uploadFile($file, $directoryPath);
                }

                $mVehicle->save();

                if($id!= null){
                    $data['vehicle_inspection_sticker_pdf'] = $mVehicle->vehicle_inspection_sticker_pdf;
                    $data['picture_fronts'] = $mVehicle->picture_fronts;
                    $data['picture_rights'] = $mVehicle->picture_rights;
                    $data['picture_lefts'] = $mVehicle->picture_lefts;
                    $data['picture_rears'] = $mVehicle->picture_rears;
                    $modifyLog = new MModifyLogs();
                    $modifyLog->writeLogWithTable( $mVehicle->getTable(),$dataBeforeUpdate,$data,$id);
                }
                DB::commit();
                if($mode=='edit'){
                    $this->backHistory();
                    \Session::flash('message',Lang::get('messages.MSG04002'));
                }else{
                    \Session::flash('message',Lang::get('messages.MSG03002'));
                }
            }catch (\Exception $e) {
                DB::rollback();
                dd($e);
            }
            return response()->json([
                'success'=>true,
                'message'=> [],
            ]);
        }
    }

    public function loadListStaff(Request $request){
        $mStaff = new MStaffs();
        $list = $mStaff->getListOption();
        if(count($list) <=0) {
            return Response()->json(array('success'=>true, 'info'=> [array('value' => '','text' => '==選択==')]));
        }
        return Response()->json(array('success'=>true, 'info'=> $list));
    }
}
