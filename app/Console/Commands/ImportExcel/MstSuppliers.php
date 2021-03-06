<?php

namespace App\Console\Commands\ImportExcel;
use App\Helpers\Common;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class MstSuppliers extends BaseImport{

    public $inputFileType = null;
    public $objReader = null;
    public $objPHPExcel = null;
    public $sheet = null;
    public $highestRow = 1;
    public $highestColumn = null;
    public $start_row = 1;

    public $excel_column_main = [
        'A' => 'mst_suppliers_cd',
        'B' => 'supplier_nm',
        'C' => 'supplier_nm_kana',
        'L' => 'zip_cd',
        'M' => 'address1',
        'N' => 'address2',
        'O' => 'phone_number',
        'AA' => 'notes',
        'AB' => 'created_at',
    ];
    public $excel_column_extra1 = [
        'A' => 'mst_suppliers_cd',
        'B' => 'supplier_nm',
    ];

    public $list_supplier_cd = [];

    public $rules = [
        'mst_suppliers_cd'  => 'required',
        'supplier_nm'  => 'required|length:200',
        'supplier_nm_kana'  => 'nullable|length:200',
        'zip_cd'  => 'nullable|length:7',
        'prefectures_cd'=> 'nullable|length:2',
        'address1'  => 'nullable|length:200',
        'address2'  => 'nullable|length:200',
        'phone_number'  => 'phone_number|nullable|length:20',
        'notes'=> 'nullable|length:50',
        'created_at'=> 'required',
        'modified_at'=> 'required',
    ];

    public $column_name = [
        'mst_suppliers_cd' => '社員CD',
        'supplier_nm'=> '社員名',
        'supplier_nm_kana'=> '社員名かな',
        'supplier_nm_formal'=> '社員名',
        'supplier_nm_kana_formal'=> '社員名かな',
        'zip_cd'=> '郵便番号',
        'prefectures_cd'=> '住所１',
        'address1'=> '住所１',
        'address2'=> '住所２',
        'phone_number'=> '電話番号',
        'notes'=> '備考',
        'created_at'=> '登録日',
        'modified_at'=> '最終更新日',
    ];
    public $column_name_extra1 = [
        'mst_suppliers_cd' => '得意先番号',
        'supplier_nm'=> '得意先名',
        'supplier_nm_formal'=> '得意先名',
    ];

    public $consumption_tax_calc_unit_id = null;
    public $rounding_method = null;


    public function run(){
        if( !file_exists(config('params.import_file_path.mst_suppliers.main.path')) ){
            echo "File don't exist";
        }else {
            if (!empty(Lang::trans("log_import.begin_start", ["table" => $this->tableLabel[$this->table]]))) {
                $this->log("data_convert", Lang::trans("log_import.begin_start", ["table" => $this->tableLabel[$this->table]]));
            }
            $mGeneralPurposes = new MGeneralPurposes();
            $this->consumption_tax_calc_unit_id = $mGeneralPurposes->getDateIDByDateNmAndDataKB(config('params.data_kb.consumption_tax_calc_unit'), '請求単位');
            $this->rounding_method = $mGeneralPurposes->getDateIDByDateNmAndDataKB(config('params.data_kb.rounding_method'), '四捨五入');
            $this->readingMainFile();
            $this->readingExtraFile();
            if (!empty(Lang::trans("log_import.end_read"))) {
                $this->log("data_convert", Lang::trans("log_import.end_read", [
                    "numRead" => $this->numRead,
                    "numNormal" => $this->numNormal,
                    "numErr" => $this->numErr,
                    "table" => $this->tableLabel[$this->table],
                ]));
            }
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
        $error_fg = false;
        $this->getDataFromExcel(config('params.import_file_path.mst_suppliers.main.path'));
        $this->start_row = 2;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $error_fg = false;
            $record = array();
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if($rowData[$row]['D'] ==3 && $row > 1) {
                $this->numRead++;
                foreach ($rowData[$row] as $pos => $value) {
                    if (isset($excel_column[$pos])) {
                        switch ($excel_column[$pos]) {
                            case 'created_at':
                                $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyy/mm/dd hh:mm:ss');
                                break;
                            case 'supplier_nm':
                                $record[$excel_column[$pos]] = (string)$value;
                                break;
                            case 'supplier_nm_kana':
                                $record[$excel_column[$pos]] = $value;
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
                $record['modified_at'] = isset($record['created_at']) ? $record['created_at'] : null;
                $record['consumption_tax_calc_unit_id'] = $this->consumption_tax_calc_unit_id ? $this->consumption_tax_calc_unit_id->date_id : null;
                $record['rounding_method_id'] = $this->rounding_method ? $this->rounding_method->date_id : null;
                array_push($this->list_supplier_cd, $record['mst_suppliers_cd']);
                $this->validate($record,$row, $this->column_name, config('params.import_file_path.mst_suppliers.main.fileName'),$error_fg);
                $record['supplier_nm_formal'] = $record['supplier_nm'];
                $this->insertDB($error_fg, $row, $record,config('params.import_file_path.mst_suppliers.main.fileName'));
            }
        }
        unset($this->objPHPExcel);
        unset($this->objReader);
    }

    public function readingExtraFile(){
        $currentTime = date("Y/m/d H:i:s ");
        $excel_column = $this->excel_column_main;
        $error_fg = false;
        $this->getDataFromExcel(config('params.import_file_path.mst_suppliers.extra1.path'));
        $this->start_row = 2;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            $error_fg = false;
            $record = array();
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if(!in_array($rowData[$row]['A'], $this->list_supplier_cd)) {
                $this->numRead++;
                foreach ($rowData[$row] as $pos => $value) {
                    if (isset($excel_column[$pos])) {
                        $record[$excel_column[$pos]] = $value!= "" ? (string)$value : null;
                    }
                }
                $record['created_at'] = $currentTime;
                $record['modified_at'] = $currentTime;
                $record['consumption_tax_calc_unit_id'] = $this->consumption_tax_calc_unit_id ? $this->consumption_tax_calc_unit_id->date_id : null;
                $record['rounding_method_id'] = $this->rounding_method ? $this->rounding_method->date_id : null;
                $this->validate($record,$row, $this->column_name_extra1, config('params.import_file_path.mst_suppliers.extra1.fileName'),$error_fg);
                $record['supplier_nm_formal'] = $record['supplier_nm'];
                $this->insertDB($error_fg, $row, $record, config('params.import_file_path.mst_suppliers.extra1.fileName'));
            }
        }
        unset($this->objPHPExcel);
        unset($this->objReader);
    }

    protected function validate(&$record, $row, $column_name, $fileName, &$error_fg){
        if (DB::table('mst_suppliers')->where('mst_suppliers_cd', '=', $record['mst_suppliers_cd'])->whereNull('deleted_at')->exists()) {
            $error_fg = true;
            $this->log("DataConvert_Err_ID_Match", Lang::trans("log_import.existed_record_in_db", [
                "fileName" => $fileName,
                "fieldName" => $column_name['mst_suppliers_cd'],
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
                            "DBvalue" => 'null',
                        ]));
                        $record[$field] = null;
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

    protected function insertDB($error_fg, $row, $record, $fileName){
        if (!$error_fg) {
            DB::beginTransaction();
            try {
                if (!empty($record)) {
                    if(isset($record['supplier_nm_kana'])){
                        $record['supplier_nm_kana'] = Common::convertToKanaExcel($record['supplier_nm_kana']);
                        $record['supplier_nm_kana_formal'] = Common::convertToKanaExcel($record['supplier_nm_kana']);
                    }
                    DB::table('mst_suppliers')->insert($record);
                    DB::commit();
                    $this->numNormal++;
                }
            } catch (\Exception $e) {
                DB::rollback();
                $this->numErr++;
                $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                    "fileName" => $fileName,
                    "row" => $row,
                    "errorDetail" => $e->getMessage(),
                ]));
            }
        } else {
            $this->numErr++;
        }
    }
}