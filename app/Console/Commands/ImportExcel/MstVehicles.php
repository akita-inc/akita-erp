<?php
namespace App\Console\Commands\ImportExcel;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MVehicles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
class MstVehicles extends BaseImport
{
    public $list_etc_numbers = [];
    public $inputFileType = null;
    public $objReader = null;
    public $objPHPExcel = null;
    public $sheet = null;
    public $highestRow = 1;
    public $highestColumn = null;
    public $start_row = 1;

    public $excel_column_main = [
        'A'=>'vehicles_cd',
        'B'=>'vehicles_kb',
        'C'=>'registration_numbers',
        'I'=>'vehicle_size_kb',
        'J'=>'vehicle_purpose_id',
        'AF'=>'land_transport_office_cd',
        'F' => 'registration_dt',
        'G' => 'first_year_registration_dt',
        'H' => 'vehicle_classification_id',
        'K' => 'private_commercial_id',
        'L' => 'car_body_shape_id',
        'M' => 'vehicle_id',
        'O' => 'seating_capacity',
        'P' => 'max_loading_capacity',
        'Q' => 'vehicle_body_weights',
        'R' => 'vehicle_total_weights',
        'S' => 'frame_numbers',
        'U' => 'vehicle_lengths',
        'V' => 'vehicle_widths',
        'W' => 'vehicle_heights',
        'Z' => 'axle_loads_ff',
        'AA' => 'axle_loads_fr',
        'AB' => 'axle_loads_rf',
        'AC' => 'axle_loads_rr',
        'N' => 'vehicle_types',
        'T' => 'engine_typese',
        'X' => 'total_displacements',
        'Y' => 'kinds_of_fuel_id',
        'AE' => 'user_base_locations',
        'AD' => 'expiry_dt',
        'AL' => 'mst_staff_cd',
        'AH' => 'personal_insurance_prices',
        'AI' => 'property_damage_insurance_prices',
        'AJ' => 'vehicle_insurance_prices',
        'AW' => 'dispose_dt',
        'AX' => 'created_at',
        'BA' => 'modified_at',
    ];
    public $excel_column_extra_1_sheet_1 = [
        'N' => 'bed_fg',
        'O' => 'refrigerator_fg',
        'V' => 'drive_system_id',
        'W' => 'drive_system_id',
        'X' => 'drive_system_id',
        'Y' => 'transmissions_id',
        'Z' => 'transmissions_id',
        'AA' => 'suspensions_cd',
        'AB' => 'suspensions_cd',
        'AC' => 'suspensions_cd',
        'AD' => 'tank_capacity_1',
        'AE' => 'tank_capacity_2',
        'AI' => 'floor_roller_fg',
        'AJ' => 'floor_joloda_conveyor_fg',
        'AK' => 'power_gate_cd',
    ];
    public $excel_column_extra_1_sheet_2 = [
        'N' => 'bed_fg',
        'O' => 'refrigerator_fg',
        'Z' => 'transmissions_id',
        'AA' => 'transmissions_id',
        'AB' => 'suspensions_cd',
        'AC' => 'suspensions_cd',
        'AD' => 'suspensions_cd',
        'AE' => 'tank_capacity_1',
        'AF' => 'tank_capacity_2',
        'AJ' => 'floor_joloda_conveyor_fg',
        'AK' => 'power_gate_cd',
    ];

    public $data_extra_file_1 = [];
    public $data_extra_file_2 = [];
    public $data_extra_file_3 = [];


    public $numRead = 0;
    public $numNormal = 0;
    public $numErr = 0;

    public $rules = [
        'vehicles_cd'=>'required|one_byte_number|length:10|number_range|unique:mst_vehicles,vehicles_cd,NULL,id,deleted_at,NULL',
        'vehicles_kb'=>'required|length:11',
        'registration_numbers'=>'required|length:50',
        'mst_business_office_id'=>'required|length:5',
        'vehicle_size_kb'=>'nullable|length:11',
        'vehicle_purpose_id'=>'nullable|length:11',
        'land_transport_office_cd'=>'nullable|length:11',
        'first_year_registration_dt'=>'nullable|date_format_custom:Ym',
        'vehicle_classification_id'=>'nullable|length:11',
        'private_commercial_id'=>'nullable|length:11',
        'car_body_shape_id'=>'nullable|length:11',
        'vehicle_id'=>'nullable|length:11',
        'seating_capacity'=>'nullable|length:11',
        'max_loading_capacity'=>'nullable|length:11',
        'vehicle_body_weights'=>'nullable|length:11',
        'vehicle_total_weights'=>'nullable|length:11',
        'frame_numbers'=>'nullable|length:50',
        'vehicle_lengths'=>'nullable|length:4',
        'vehicle_widths'=>'nullable|length:3',
        'vehicle_heights'=>'nullable|length:3',
        'axle_loads_ff'=>'nullable|length:11',
        'axle_loads_fr'=>'nullable|length:11',
        'axle_loads_rf'=>'nullable|length:11',
        'axle_loads_rr'=>'nullable|length:11',
        'vehicle_types'=>'nullable|length:50',
        'engine_typese'=>'nullable|length:50',
        'total_displacements'=>'nullable|length:11',
        'kinds_of_fuel_id'=>'nullable|length:11',
        'user_base_locations'=>'nullable|length:200',
        'personal_insurance_prices'=>'nullable|length:11',
        'property_damage_insurance_prices'=>'nullable|length:11',
        'vehicle_insurance_prices'=>'nullable|length:11',
        'created_at'=>'required',
        'modified_at'=>'required',
    ];
    public $rules_extra_1 = [
        'etc_numbers'=>'nullable|length:19',
    ];
    public $rules_extra_2 = [
        'bed_fg'=>'nullable|length:11',
        'refrigerator_fg'=>'nullable|length:11',
        'drive_system_id'=>'nullable|length:11',
        'transmissions_id'=>'nullable|length:11',
        'transmissions_notes'=>'nullable|length:50',
        'suspensions_cd'=>'nullable|length:11',
        'tank_capacity_1'=>'nullable|length:11',
        'tank_capacity_2'=>'nullable|length:11',
        'floor_roller_fg'=>'nullable|length:1',
        'floor_joloda_conveyor_fg'=>'nullable|length:1',
        'power_gate_cd'=>'nullable|length:11',
    ];
    public $rules_extra_3 = [
        'acquisition_amounts'=>'nullable|length:12',
        'durable_years'=>'nullable|one_byte_number|length:11',
    ];

    public $column_name = [
        'vehicles_cd'=> '車両CD',
        'door_number'=> '',
        'vehicles_kb'=> '車両区分',
        'registration_numbers'=> '登録番号',
        'mst_business_office_id'=> '車両所属CD',
        'vehicle_size_kb'=> '小中大区分',
        'vehicle_purpose_id'=> '用途',
        'land_transport_office_cd'=> '陸運支局CD',
        'vehicle_inspection_sticker_pdf'=> '',
        'registration_dt'=> '登録年月日',
        'first_year_registration_dt'=> '初年度登録年月',
        'vehicle_classification_id'=> '種別',
        'private_commercial_id'=> '区分',
        'car_body_shape_id'=> '車体形状',
        'vehicle_id'=> '車名',
        'seating_capacity'=> '定員',
        'max_loading_capacity'=> '最大積載量',
        'vehicle_body_weights'=> '車両重量',
        'vehicle_total_weights'=> '車両総重量',
        'frame_numbers'=> '車台番号',
        'vehicle_lengths'=> '長さ',
        'vehicle_widths'=> '幅',
        'vehicle_heights'=> '高さ',
        'axle_loads_ff'=> '前前車軸',
        'axle_loads_fr'=> '前後車軸',
        'axle_loads_rf'=> '後前車軸',
        'axle_loads_rr'=> '後後車軸',
        'vehicle_types'=> '形式',
        'engine_typese'=> '原動機形式',
        'total_displacements'=> '排気量',
        'rated_outputs'=> '',
        'kinds_of_fuel_id'=> '燃料種類',
        'user_base_locations'=> '使用本拠地',
        'expiry_dt'=> '車検日',
        'mst_staff_cd'=> '社員CD',
        'personal_insurance_prices'=> '対人保険',
        'property_damage_insurance_prices'=> '対物保険',
        'vehicle_insurance_prices'=> '車両保険',
        'dispose_dt'=> '廃車日',
        'created_at'=> '登録日',
        'modified_at'=> '最終更新日',
        'etc_numbers'=>'車載機器',
        'tank_capacity_1' => 'タンク1',
        'tank_capacity_2' => 'タンク2',
        'acquisition_amounts'=>'取得金額',
        'durable_years'=>'耐用年数',
    ];

    public function run(){
        $this->readingVehicleExtraFile1();
        $this->readingVehicleExtraFile2();
        $this->readingVehicleExtraFile3();
        if( !empty( Lang::trans("log_import.begin_start", ["table" => $this->tableLabel[$this->table]]))){
            $this->log("data_convert",Lang::trans("log_import.begin_start",["table" => $this->tableLabel[$this->table]]));
        }
        $this->readingMainFile();
        if( !empty( Lang::trans("log_import.end_read") ) ){
            $this->log("data_convert",Lang::trans("log_import.end_read",[
                "numRead" => $this->numRead,
                "numNormal"=> $this->numNormal,
                "numErr" => $this->numErr,
                "table" => $this->tableLabel[$this->table],
            ]));
        }
    }

    public function getDataFromExcel( $path){

        $this->inputFileType = \PHPExcel_IOFactory::identify($path);
        $this->objReader = \PHPExcel_IOFactory::createReader($this->inputFileType );
        $this->objPHPExcel = $this->objReader->load($path);

        $this->sheet = $this->objPHPExcel->getSheet(0);
        $this->highestRow =  $this->sheet->getHighestRow();
        $this->highestColumn =  $this->sheet->getHighestColumn();

    }

    public function readingMainFile(){
        $excel_column = $this->excel_column_main;
        $data = [];
        $keys = [];
        $mBusinessOffices = new MBusinessOffices();
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.main.path'));
        $this->start_row = 1;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $record = array();
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if($row==1){
                $keys = $rowData[$row];
                continue;
            }
            foreach ($rowData[$row] as $pos => $value) {
                if (isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]) {
                        case 'created_at':
                        case 'modified_at':
                            $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyy/mm/dd hh:mm:ss');
                            break;
                        case 'first_year_registration_dt':
                            $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyymm');
                            break;
                        case 'registration_dt':
                        case 'expiry_dt':
                        case 'dispose_dt':
                            $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyy/mm/dd');
                            break;
                        case 'vehicles_kb':
                        case 'vehicle_size_kb':
                        case 'vehicle_purpose_id':
                        case 'land_transport_office_cd':
                        case 'vehicle_classification_id':
                        case 'private_commercial_id':
                        case 'car_body_shape_id':
                        case 'vehicle_id':
                        case 'kinds_of_fuel_id':
                            switch ($excel_column[$pos]) {
                                case 'vehicles_kb':
                                    $data_kb = config('params.data_kb')['vehicles_kb'];
                                    break;
                                case 'vehicle_size_kb':
                                    $data_kb = config('params.data_kb')['vehicle_size_kb'];
                                    break;
                                case 'vehicle_purpose_id':
                                    $data_kb = config('params.data_kb')['vehicle_purpose'];
                                    break;
                                case 'land_transport_office_cd':
                                    $data_kb = config('params.data_kb')['land_transport_office_cd'];
                                    break;
                                case 'vehicle_classification_id':
                                    $data_kb = config('params.data_kb')['vehicle_classification'];
                                    break;
                                case 'private_commercial_id':
                                    $data_kb = config('params.data_kb')['private_commercial'];
                                    break;
                                case 'car_body_shape_id':
                                    $data_kb = config('params.data_kb')['car_body_shape'];
                                    break;
                                case 'vehicle_id':
                                    $data_kb = config('params.data_kb')['vehicle'];
                                    break;
                                case 'kinds_of_fuel_id':
                                    $data_kb = config('params.data_kb')['kinds_of_fuel'];
                                    break;
                            }
                            $result = $this->checkExistDataAndInsert($data_kb, $value,config('params.import_file_path.mst_vehicles.main.fileName'),$keys[$pos], $row );
                            if ($result) {
                                $record[$excel_column[$pos]] = (string)$result;
                            } else {
//                                break 2;
                            }
                            break;
                        default:
                            $record[$excel_column[$pos]] = (string)$value;
                    }

                }
            }

            $findOffice = $mBusinessOffices->where('mst_business_office_cd',(integer)$rowData[$row]['AG'])->whereNull('deleted_at')->first();
            if($findOffice){
                $record['mst_business_office_id'] = $findOffice->id;
            }

            $data = $record;
            if (DB::table('mst_vehicles_copy1')->where('vehicles_cd', '=', $record['vehicles_cd'])->whereNull('deleted_at')->exists()) {
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.existed_record_in_db",[
                    "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                    "fieldName" => $keys[$pos],
                    "row" => $row,
                ]));
            }
            $validator = Validator::make($data, $this->rules);

            if ($validator->fails()) {
                $failedRules = $validator->failed();
                foreach ($failedRules as $field => $errors){
                    foreach ($errors as $ruleName => $error){
                        if($ruleName=='Length'){
                            $this->log("data_convert",Lang::trans("log_import.check_length_and_trim",[
                                "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                                "excelFieldName" => $this->column_name[$field],
                                "row" => $row,
                                "excelValue" => $data[$field],
                                "tableName" => $this->table,
                                "DBFieldName" => $field,
                                "DBvalue" => substr($data[$field],0,$error[0]),
                            ]));
                            $data[$field] = substr($data[$field],0,$error[0]);
                        }else if($ruleName=='Required'){
                            $this->log("DataConvert_Err_required",Lang::trans("log_import.required",[
                                "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                                "fieldName" => $this->column_name[$field],
                                "row" => $row,
                            ]));
                        }
                    }
                }
            }


            for ($k = 1; $k <=3; $k++){
                if(isset($this->{"data_extra_file_".$k}[$record['vehicles_cd']])){
                    $data = $data + $this->{"data_extra_file_".$k}[$record['vehicles_cd']];
                    $validator = Validator::make($data, $this->{'rules_extra_'.$k});
                    if ($validator->fails()) {
                        $failedRules = $validator->failed();
                        foreach ($failedRules as $field => $errors){
                            foreach ($errors as $ruleName => $error){
                                if($ruleName=='Length'){
                                    $this->log("data_convert",Lang::trans("log_import.check_length_and_trim",[
                                        "fileName" => config('params.import_file_path.mst_vehicles.extra'.$k.'.fileName'),
                                        "excelFieldName" => $this->column_name[$field],
                                        "row" => $data['row'],
                                        "excelValue" => $data[$field],
                                        "tableName" => $this->table,
                                        "DBFieldName" => $field,
                                        "DBvalue" => substr($data[$field],0,$error[0]),
                                    ]));
                                    $data[$field] = substr($data[$field],0,$error[0]);
                                }else if($ruleName=='Required'){
                                    $this->log("DataConvert_Err_required",Lang::trans("log_import.required",[
                                        "fileName" => config('params.import_file_path.mst_vehicles.extra'.$k.'.fileName'),
                                        "fieldName" => $this->column_name[$field],
                                        "row" => $data['row'],
                                    ]));
                                }
                            }
                        }
                    }
                    unset($data['row']);
                    unset($data['sheet']);
                }else{
                    $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                        "mainFileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                        "fieldName" => $keys[$pos],
                        "row" => $row,
                        "extraFileName" => config('params.import_file_path.mst_vehicles.extra'.$k.'.fileName'),
                    ]));
                }
            }

//            dd($this->data_extra_file_3[$record['vehicles_cd']]);
            DB::beginTransaction();
            try{
                if (!empty($record)) {
                    DB::table('mst_vehicles_copy1')->insert($data);
                    DB::commit();
                    dd("sdfsdfds");
                }
            }catch (\Exception $e){
                DB::rollback();
                $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                    "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                    "row" => $row,
                    "errorDetail" => $e->getMessage(),
                ]));
            }

        }

    }

    public function readingVehicleExtraFile1(){
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra1.path'));
        $this->start_row = 2;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if(!is_null($rowData[$row]['B']) && is_numeric($rowData[$row]['B']) && !is_null($rowData[$row]['R'])){
                $this->data_extra_file_1[$rowData[$row]['B']] = [
                    'etc_numbers' => $rowData[$row]['R'],
                    'row' => $row,
                ];
            }
        }

    }

    public function readingVehicleExtraFile2()
    {
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra2.path'));
        $this->start_row = 7;
        $keys_sheet_1 = [
            'N' => 1,
            'O' => 1,
            'V' => 1,
            'W' => 2,
            'X' => 3,
            'Y' => 2,
            'Z' => 1,
            'AA' => 1,
            'AB' => 2,
            'AC' => 3,
        ];
        $keys_sheet_2 = [
            'N' => 1,
            'O' => 1,
            'Z' => 2,
            'AA' => 1,
            'AB' => 1,
            'AC' => 2,
            'AD' => 3,
        ];

        for ($i = 0; $i < 2; $i++) {
            $this->sheet = $this->objPHPExcel->setActiveSheetIndex($i);
            $this->highestRow = $this->sheet->getHighestRow();
            $this->highestColumn = $this->sheet->getHighestColumn();
            $excel_column = $this->{'excel_column_extra_1_sheet_' . ($i + 1)};
            for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
                $record = [];
                $rowData = $this->sheet->rangeToArray('A' . $row . ':' . $this->highestColumn . $row, null, false, false, true);
                if (!is_null($rowData[$row]['B'])) {
                    foreach ($rowData[$row] as $pos => $value) {
                        if ($i == 0) {
                            switch ($pos) {
                                case 'AD':
                                case 'AE':
                                    if(!is_null($value)){
                                        $record[$excel_column[$pos]] = (string)$value;
                                    }
                                    break;
                                case 'N':
                                case 'O':
                                case 'V':
                                case 'W':
                                case 'X':
                                case 'Y':
                                case 'Z':
                                case 'AA':
                                case 'AB':
                                case 'AC':
                                    if ($value != '') {
                                        $record[$excel_column[$pos]] = $keys_sheet_1[$pos];

                                        if ($pos == 'Y' || $pos == 'Z') {
                                            if ($value != '○' && $value != '〇') {
                                                $record['transmissions_notes'] = (string)$value;
                                            }
                                        }
                                    }
                                    break;
                            }
                        } else {
                            switch ($pos) {
                                case 'AE':
                                case 'AF':
                                    if(!is_null($value)){
                                        $record[$excel_column[$pos]] = (string)$value;
                                    }
                                    break;
                                case 'N':
                                case 'O':
                                case 'Z':
                                case 'AA':
                                case 'AB':
                                case 'AC':
                                case 'AD':
                                    if ($value != '') {
                                        $record[$excel_column[$pos]] = $keys_sheet_2[$pos];
                                        if ($pos == 'Z' || $pos == 'AA') {
                                            if ($value != '○' && $value != '〇') {
                                                $record['transmissions_notes'] = (string)$value;
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                    $record['row'] = $row;
                    $record['sheet'] = $i;
                    $this->data_extra_file_2[$rowData[$row]['B']] = $record;
                }
            }
        }
    }

    public function readingVehicleExtraFile3(){
        $this->getDataFromExcel(config('params.import_file_path.mst_vehicles.extra3.path'));
        $this->start_row = 3;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if(!is_null($rowData[$row]['A']) && is_numeric($rowData[$row]['A'])){
                $this->data_extra_file_3[$rowData[$row]['A']] = [
                    'acquisition_amounts' => is_null($rowData[$row]['G']) ? null :(string)$rowData[$row]['G'],
                    'durable_years' => is_null($rowData[$row]['J']) ? null : (string)$rowData[$row]['J'],
                    'row' => $row,
                ];
            }
        }
    }
}
