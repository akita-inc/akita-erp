<?php

namespace App\Http\Controllers;

use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MStaffs;
use App\Models\MVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
class VehiclesController extends Controller
{

    use ListTrait;
    public $table = "mst_vehicles";

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
            'radio_reference_date' => $data['fieldSearch']['radio_reference_date'],
            'reference_date' => date('Y-m-d', strtotime($data['fieldSearch']['reference_date'])),
        );

        $this->query->select('mst_vehicles.id',
            'mst_vehicles.vehicles_cd',
            'mst_vehicles.door_number',
            'mst_general_purposes.date_nm AS vehicles_kb_nm',
            'mst_vehicles.registration_numbers',
            'mst_business_offices.business_office_nm',
            'size.date_nm AS vehicle_size_kb_nm',
            'purpose.date_nm AS vehicle_purpose_nm',
            DB::raw("DATE_FORMAT(mst_vehicles.adhibition_start_dt, '%Y/%m/%d') as adhibition_start_dt"),
            DB::raw("DATE_FORMAT(mst_vehicles.adhibition_end_dt, '%Y/%m/%d') as adhibition_end_dt"),
            DB::raw("DATE_FORMAT(mst_vehicles.modified_at, '%Y/%m/%d') as modified_at"),
            DB::raw("DATE_FORMAT(sub.max_adhibition_end_dt, '%Y/%m/%d') as max_adhibition_end_dt")
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
            ->leftjoin(DB::raw('(select vehicles_cd, max(adhibition_end_dt) AS max_adhibition_end_dt from mst_vehicles where deleted_at IS NULL group by vehicles_cd) sub'), function ($join) {
                $join->on('sub.vehicles_cd', '=', 'mst_vehicles.vehicles_cd');
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
        if ($where['radio_reference_date'] == '1' && $where['reference_date'] != '') {
            $this->query->where('mst_vehicles.adhibition_start_dt', "<=", $where['reference_date']);
            $this->query->where('mst_vehicles.adhibition_end_dt', ">=", $where['reference_date']);
        }

        $this->query->orderby('mst_vehicles.vehicles_cd');
        $this->query->orderby('mst_vehicles.adhibition_start_dt');
    }

    public function delete($id)
    {
        $mVehicle = new MVehicles();

        if ($mVehicle->deleteVehicle($id)) {
            $response = ['data' => 'success'];
        } else {
            $response = ['data' => 'failed', 'msg' => Lang::get('messages.MSG06002')];
        }
        return response()->json($response);
    }

    public function checkIsExist($id){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->find($id);
        if (isset($mVehicle)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> Lang::trans('messages.MSG06003')));
        }
    }

    public function create(Request $request){
        $mVehicle = new MVehicles();
        $mGeneralPurposes = new MGeneralPurposes();
        $mBusinessOffices = new MBusinessOffices();
        $mStaff = new MStaffs();
        $listBusinessOffices = $mBusinessOffices->getListOption();
        $listAdminStaffs = $mStaff->getListOption();
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

        return view('vehicles.create',[
            'mVehicle' => $mVehicle,
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
            'listAdminStaffs' => $listAdminStaffs,
        ]);
    }
}