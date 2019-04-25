<?php

namespace App\Console\Commands\ImportExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use App\Models\MGeneralPurposes;
use App\Models\MBusinessOffices;

class MstBusinessOffices extends BaseImport
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
        'B' => 'business_office_nm',
        'C' => 'mst_business_office_cd',
        'D' => 'branch_office_cd',
        'E' => 'zip_cd',
        'F' => 'address1',
        'H' => 'phone_number',
        'I' => 'fax_number',
        'J' => 'ip_phone_number'
    ];
    public $rules = [
        'business_office_nm'=>'required|length:50',
        'mst_business_office_cd'=>'required',
        'branch_office_cd'=>'nullable',
        'zip_cd'=>'nullable|length:7',
        'prefectures_cd' =>'nullable|length:2',
        'address1'  => 'nullable|length:20',
        'phone_number'=>'nullable|length:20',
        'fax_number'=>'nullable|length:20',
        'ip_phone_number'=>'nullable|length:20'
    ];
    public $column_name = [
        'business_office_nm'=>'営業所名',
        'mst_business_office_cd'=>'CD',
        'branch_office_cd'=>'支店CD',
        'zip_cd'=>'郵便番号',
        'address1'=>'所在地',
        'phone_number'=>'電話番号',
        'fax_number'=>'ＦＡＸ',
        'ip_phone_number'=>'IP電話番号'
    ];

    public function run(){
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
        $mGeneralPurposes = new MGeneralPurposes();
        $excel_column = $this->excel_column_main;
        $currentTime = date("Y/m/d H:i:s ");
        $error_fg = false;
        $mBusinessOffices = new MBusinessOffices();
        $disp_number = $mBusinessOffices->getMaxDispNumber() + 1;
        $this->getDataFromExcel(config('params.import_file_path.mst_business_offices.main.path'));
        $this->start_row = 4;
        for($row = $this->start_row; $row <= $this->highestRow;$row++){
            $error_fg = false;
            $record = array();
            $rowData = $this->sheet->rangeToArray('B' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if($row == 1){
                $keys = $rowData[$row];
                continue;
            }
            $this->numRead++;
            $record['created_at'] = $currentTime;
            $record['modified_at'] = $currentTime;
            foreach ($rowData[$row] as $pos => $value) {
                if (isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]) {
                        case 'business_office_nm':
                            $val = str_replace(" ", "　", $value);
                            $record[$excel_column[$pos]] = str_replace("　", "", $val);
                            break;
                        case 'zip_cd':
                            $record[$excel_column[$pos]] = $value!= "" ? str_replace("-", "", $value) : null;
                            break;
                        case 'address1':
                            $prefectures_cd = $mGeneralPurposes->getPrefCdByPrefName($value);
                            if ($prefectures_cd) {
                                $record['prefectures_cd'] = $prefectures_cd['date_id'];
                                $record[$excel_column[$pos]] = mb_substr($value, mb_strlen($prefectures_cd['date_nm']));
                            } else {
                                $record[$excel_column[$pos]] = $value;
                            }
                            break;
                        default:
                            $record[$excel_column[$pos]] = $value!= "" ? (string)$value : null;
                    }
                }
            }
            $this->validate($record,$row, $this->column_name, config('params.import_file_path.mst_business_offices.main.fileName'),$error_fg);
            $this->insertDB($error_fg, $row, $record,$disp_number);
        }
    }

    protected function insertDB($error_fg, $row, $record,&$disp_number){
        if (!$error_fg) {
            DB::beginTransaction();
            try {
                if (!empty($record)) {
                    $record['disp_number'] = $disp_number;
                    DB::table('mst_business_offices')->insert($record);
                    DB::commit();
                    $disp_number++;
                    $this->numNormal++;
                }
            } catch (\Exception $e) {
                DB::rollback();
                $this->numErr++;
                $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                    "fileName" => config('params.import_file_path.mst_business_offices.main.fileName'),
                    "row" => $row,
                    "errorDetail" => $e->getMessage(),
                ]));
            }
        } else {
            $this->numErr++;
        }
    }
    protected function validate(&$record, $row, $column_name, $fileName, &$error_fg){
        if (DB::table('mst_business_offices')->where('mst_business_office_cd', '=', $record['mst_business_office_cd'])->whereNull('deleted_at')->exists() && $record['mst_business_office_cd']!='' && is_numeric($record['mst_business_office_cd'])) {
            $error_fg = true;
            $this->log("DataConvert_Err_ID_Match", Lang::trans("log_import.existed_record_in_db", [
                "fileName" => $fileName,
                "fieldName" => $column_name['mst_business_office_cd'],
                "row" => $row,
            ]));
        }
        $validator = Validator::make($record, $this->rules);

        if ($validator->fails()) {
            $failedRules = $validator->failed();
            foreach ($failedRules as $field => $errors) {
                foreach ($errors as $ruleName => $error) {
                    if ($ruleName == 'Length') {
                        $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                            "fileName" => $fileName,
                            "excelFieldName" => $column_name[$field],
                            "row" => $row,
                            "excelValue" => $record[$field],
                            "tableName" => $this->table,
                            "DBFieldName" => $field,
                            "DBvalue" => mb_substr($record[$field], 0, $error[0]),
                        ]));
                        $record[$field] = mb_substr($record[$field], 0, $error[0]);
                    } else if ($ruleName == 'Required') {
                        $error_fg = true;
                        $this->log("DataConvert_Err_required", Lang::trans("log_import.required", [
                            "fileName" => $fileName,
                            "fieldName" => $column_name[$field],
                            "row" => $row,
                        ]));
                    }
                }
            }
        }
    }
}