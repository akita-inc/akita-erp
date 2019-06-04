<?php

namespace App\Models;

use App\Helpers\TimeFunction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MVehicles extends Model
{
    use SoftDeletes;

    protected $table = "mst_vehicles";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
        'vehicles_cd' => '車両コード',
        'door_number' => 'ドア番',
        'vehicles_kb' => '車両区分',
        'registration_numbers1' => '自動車登録番号1',
        'registration_numbers2' => '自動車登録番号2',
        'registration_numbers3' => '自動車登録番号3',
        'registration_numbers4' => '自動車登録番号4',
        'mst_business_office_id' => '営業所',
        'vehicle_size_kb' => '小中大区分',
        'vehicle_purpose_id' => '用途',
        'land_transport_office_cd' => '陸運支局',
        'vehicle_inspection_sticker_pdf' => '車検証PDF',
        'registration_dt' => '自動車登録年月日',
        'first_year_registration_dt' => '初度登録年月',
        'vehicle_classification_id' => '自動車種別',
        'private_commercial_id' => '自家用・事業用の別',
        'car_body_shape_id' => '車体の形状',
        'vehicle_id' => '車名',
        'seating_capacity' => '定員（人）',
        'max_loading_capacity' => '最大積載量（Kg）',
        'vehicle_body_weights' => '車両重量（Kg）',
        'vehicle_total_weights' => '車両総重量（Kg）',
        'frame_numbers' => '車台番号',
        'vehicle_lengths' => '長さ（cm）',
        'vehicle_widths' => '幅（cm）',
        'vehicle_heights' => '高さ（cm）',
        'axle_loads_ff' => '前前軸重（Kg）',
        'axle_loads_fr' => '前後軸重（Kg）',
        'axle_loads_rf' => '後前軸重（Kg）',
        'axle_loads_rr' => '後後軸重（Kg）',
        'vehicle_types' => '型式',
        'engine_typese' => '原動機の型式',
        'total_displacements' => '総排気量（L）',
        'rated_outputs' => '定格出力（KW）',
        'kinds_of_fuel_id' => '燃料の種類',
        'type_designation_numbers' => '型式指定番号',
        'id_segment_numbers' => '識別区分番号',
        'owner_nm' => '所有者－氏名または名称',
        'owner_address' => '所有者－住所',
        'user_nm' => '使用者－氏名または名称',
        'user_address' => '使用者－住所',
        'user_base_locations' => '使用者－本拠の位置',
        'expiry_dt' => '使用者－有効期間の満了する日',
        'car_inspections_notes' => '車検証備考',
        'digital_tachograph_numbers' => 'デジタコ車載器No.',
        'etc_numbers' => 'ETC車載器No.',
        'drive_recorder_numbers' => 'ドラレコNo.',
        'bed_fg' => 'ベッドの有無',
        'refrigerator_fg' => '冷蔵冷凍機の有無',
        'drive_system_id' => '駆動',
        'transmissions_id' => 'ミッション',
        'transmissions_notes' => 'ミッション備考',
        'suspensions_cd' => 'サスペンション',
        'tank_capacity_1' => '燃料タンクの容量１',
        'tank_capacity_2' => '燃料タンクの容量２',
        'loading_inside_dimension_capacity_length' => '積込可能内寸－長さ（cm）',
        'loading_inside_dimension_capacity_width' => '積込可能内寸－幅（cm）',
        'loading_inside_dimension_capacity_height' => '積込可能内寸－高さ（cm）',
        'snowmelt_fg' => '融雪',
        'double_door_fg' => '観音扉',
        'floor_iron_plate_fg' => '床・鉄板',
        'floor_sagawa_embedded_fg' => '床・佐川仕様埋込式',
        'floor_roller_fg' => '床・ローラー',
        'floor_joloda_conveyor_fg' => '床・ジョルダー及びコンベアー',
        'power_gate_cd' => 'パワーゲート',
        'vehicle_delivery_dt' => '納車日',
        'specification_notes' => '仕様に関する備考',
        'mst_staff_cd' => '車輌管理責任者',
        'personal_insurance_prices' => '対人保険料（円）',
        'property_damage_insurance_prices' => '対物保険料（円）',
        'vehicle_insurance_prices' => '車両保険料（円）',
        'picture_fronts' => '写真　前',
        'picture_rights' => '写真　側面右',
        'picture_lefts' => '写真　側面左',
        'picture_rears' => '写真　後',
        'acquisition_amounts' => '取得金額（円）',
        'acquisition_amortization' => '償却回数（回）',
        'durable_years' => '耐用年数（年）',
        'tire_sizes' => 'タイヤサイズ',
        'battery_sizes' => 'バッテリーサイズ',
        'dispose_dt' => '売却または廃車日',
        'notes' => '備考',
    ];

    public $rules = [

    ];

    public function getHistoryNearest($vehicles_cd, $adhibition_end_dt) {
        $vehicles = new MVehicles();
        $vehicles = $vehicles->where('vehicles_cd', '=', $vehicles_cd)
                                ->where("adhibition_end_dt", "<", $adhibition_end_dt)
                                ->orderByDesc("adhibition_end_dt");
        return $vehicles->first();
    }

    public function deleteVehicle($id){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->find($id);

        DB::beginTransaction();
        try
        {
            $mVehicle->delete();
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }

    public function getVehiclesByCondition($where = array()){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->select(DB::raw('mst_vehicles.*'));

        // 検索条件
        if (isset($where['vehicles_cd']) && $where['vehicles_cd'] != '')
            $mVehicle = $mVehicle->where('vehicles_cd', "=", $where['vehicles_cd']);
        if (isset($where['id']) && $where['id'] != '')
            $mVehicle = $mVehicle->where('id', "!=", $where['id']);
        if (isset($where['adhibition_start_dt']) && $where['adhibition_start_dt'] != '')
            $mVehicle = $mVehicle->where('adhibition_start_dt', "<=", $where['adhibition_start_dt']);
        $mVehicle->orderBy('vehicles_cd', 'adhibition_start_dt');

        return $mVehicle->get();
    }
    
    public function getLastedVehicle($vehicles_cd){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->where('vehicles_cd', '=', $vehicles_cd)
            ->orderByDesc("adhibition_start_dt");
        return $mVehicle->first();
    }

    public function editVehicle($id,$adhibition_start_dt){
        $mVehicle = new MVehicles();
        $mVehicle = $mVehicle->find($id);

        DB::beginTransaction();
        try
        {
            $historySupplier = $this->getHistoryNearest($mVehicle->vehicles_cd, $mVehicle->adhibition_end_dt);
            if (isset($historySupplier)) {
                $historySupplier->adhibition_end_dt = TimeFunction::subOneDay($adhibition_start_dt);
                $historySupplier->save();
            }
            DB::commit();
            return true;
        } catch (\Exception $ex){
            DB::rollBack();
            return false;
        }
    }
}
