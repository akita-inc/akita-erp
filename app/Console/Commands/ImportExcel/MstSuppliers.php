<?php

namespace App\Console\Commands\ImportExcel;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class MstSuppliers {

    public $inputFileType = null;
    public $objReader = null;
    public $objPHPExcel = null;
    public $sheet = null;
    public $highestRow = 1;
    public $highestColumn = null;
    public $start_row = 1;

    public $excel_column_main = [

    ];
    public $excel_column_extra_1= [

    ];

    public $rules = [

    ];

    public $column_name = [

    ];

    public function run(){

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
        $error_fg = false;
        $mBusinessOffices = new MBusinessOffices();
        $this->getDataFromExcel(config('params.import_file_path.mst_suppliers.main.path'));
        $this->start_row = 1;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $this->numRead++;
            $error_fg = false;
            $record = array();
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if($row==1){
                $keys = $rowData[$row];
                $this->numNormal++;
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
                $error_fg = true;
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.existed_record_in_db",[
                    "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                    "fieldName" => $keys[$pos],
                    "row" => $row,
                ]));
            }
            $validator = Validator::make($data, $this->rules);

            if ($validator->fails()) {
                $error_fg = true;
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
                        $error_fg = true;
                        $failedRules = $validator->failed();
                        foreach ($failedRules as $field => $errors){
                            foreach ($errors as $ruleName => $error){
                                if($ruleName=='Length'){
                                    $this->log("data_convert",Lang::trans("log_import.check_length_and_trim",[
                                        "fileName" => config('params.import_file_path.mst_vehicles.extra'.$k.'.fileName'). ($k==2 ? '.'.$data['sheet'] : ''),
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
                    $error_fg = true;
                    $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                        "mainFileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                        "fieldName" => $keys[$pos],
                        "row" => $row,
                        "extraFileName" => config('params.import_file_path.mst_vehicles.extra'.$k.'.fileName'),
                    ]));
                }
            }
            if(!$error_fg){
                DB::beginTransaction();
                try{
                    if (!empty($record)) {
                        DB::table('mst_vehicles_copy1')->insert($data);
                        DB::commit();
                        $this->numNormal++;
                    }
                }catch (\Exception $e){
                    DB::rollback();
                    $this->numErr++;
                    $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                        "fileName" => config('params.import_file_path.mst_vehicles.main.fileName'),
                        "row" => $row,
                        "errorDetail" => $e->getMessage(),
                    ]));
                }
            }else{
                $this->numErr++;
            }
        }
    }
}