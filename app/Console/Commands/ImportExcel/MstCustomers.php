<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 04/22/2019
 * Time: 8:54 AM
 */

namespace App\Console\Commands\ImportExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use App\Models\MGeneralPurposes;

class MstCustomers extends BaseImport
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
        'A' =>'mst_customers_cd',
        'B' =>'customer_nm',
        'C' =>'customer_nm_kana',
        'D' =>'person_in_charge_last_nm',
        'E' =>'zip_cd',
        'F' =>'address1',//prefectures_cd
        'G' =>'address2',
        'H' =>'phone_number',
        'I' =>'bundle_dt',
        'L' =>'discount_rate',
        'N' =>'notes',
        'O' =>'mst_account_titles_id',
        'U' =>'created_at',
        'X' =>'modified_at',
    ];
    public $rules = [
        'mst_customers_cd' =>'required|length:5',
        'customer_nm' =>'nullable|length:200',
        'customer_nm_kana' =>'kana_custom|nullable|length:200',
        'customer_nm_formal' =>'nullable|length:200',
        'customer_nm_kana_formal' =>'kana_custom|nullable|length:200',
        'person_in_charge_last_nm' =>'nullable|length:25',
        'person_in_charge_first_nm' =>'nullable|length:25',
        'zip_cd' =>'nullable|length:7',
        'prefectures_cd' =>'nullable|length:2',
        'address2' =>'nullable|length:20',
        'phone_number' =>'nullable|length:20',
        'bundle_dt' =>'nullable|length:11',
        //'discount_rate' =>'nullable|length:3',
        'notes' =>'nullable|length:50',
        'mst_account_titles_id' =>'nullable|length:11',
        'created_at' =>'required',
        'modified_at' =>'required',
    ];
    public $column_name = [
        'mst_customers_cd' =>'得意先CD',
        'customer_nm' =>'得意先名',
        'customer_nm_kana' =>'得意先名かな',
        'person_in_charge_last_nm' =>'担当者名',
        'zip_cd' =>'郵便番号',
        'prefectures_cd' =>'住所１',
        'address1' =>'住所１',
        'address2' =>'住所２',
        'phone_number' =>'電話番号',
        'bundle_dt' =>'請求締日',
        'discount_rate' =>'値引率',
        'notes' =>'備考',
        'mst_account_titles_id' =>'売上勘定科目',
        'created_at' =>'登録日',
        'modified_at' =>'最終更新日',
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
        $mst_customers_relate_cds = $this->readingExtraFile();
        $excel_column = $this->excel_column_main;
        $data = [];
        $keys = [];
        $error_fg = false;
        $this->getDataFromExcel(config('params.import_file_path.mst_customers.main.path'));
        $this->start_row = 1;
        for($row = $this->start_row; $row <= $this->highestRow;$row++){
            $error_fg = false;
            $record = array();
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            if($row == 1){
                $keys = $rowData[$row];
                /*$this->numRead++;
                $this->numNormal++;*/
                continue;
            }
            $this->numRead++;
            foreach ($rowData[$row] as $pos => $value) {
                if(isset($excel_column[$pos])){
                    switch ($excel_column[$pos]) {
                        case 'created_at':
                        case 'modified_at':
                            $record[$excel_column[$pos]] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyy/mm/dd hh:mm:ss');
                            break;
                        case 'customer_nm':
                            $record[$excel_column[$pos]] = (string)$value;
                            $record['customer_nm_formal'] = (string)$value;
                            break;
                        case 'customer_nm_kana':
                            $record[$excel_column[$pos]] = mb_convert_kana($value);
                            $record['customer_nm_kana_formal'] = mb_convert_kana($value);
                            break;
                        case 'person_in_charge_last_nm':
                            $value = str_replace(' ', '　', $value);
                            $names = explode('　',$value);
                            if(count($names) > 1){
                                $record[$excel_column[$pos]] = $names[0];
                                $record['person_in_charge_first_nm'] = $names[1];
                            }
                            if(count($names) == 1){
                                $record[$excel_column[$pos]] = $names[0];
                            }
                            break;
                        case 'zip_cd':
                            $record[$excel_column[$pos]] = str_replace("-", "", $value);
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
                        case 'mst_customers_cd':
                            $val = (string)$value;
                            if(array_key_exists($val, $mst_customers_relate_cds)){
                                $record['bill_mst_customers_cd'] = $mst_customers_relate_cds[$value];
                            }
                            $record[$excel_column[$pos]] = (string)$value;
                            break;
                        default:
                            $record[$excel_column[$pos]] = (string)$value;
                    }
                }
            }
            $record['consumption_tax_calc_unit_id'] = $mGeneralPurposes->getDateIdByDateKbAndDateNm(config('params.data_kb')['consumption_tax_calc_unit'],'請求単位');
            $record['rounding_method_id'] = $mGeneralPurposes->getDateIdByDateKbAndDateNm(config('params.data_kb')['rounding_method'],'四捨五入');
            $record['enable_fg'] = 1;

            //$record['bill_mst_customers_cd'] = isset($mst_customers_relate_cds[$record['mst_customers_cd']])?$mst_customers_relate_cds[$record['mst_customers_cd']]:null;

            $this->validate($record,$row, $this->column_name, config('params.import_file_path.mst_customers.main.fileName'),$error_fg);
            $this->insertDB($error_fg, $row, $record);
        }
    }

    public function readingExtraFile(){
        $mst_customers_cds = [];
        $currentTime = date("Y/m/d H:i:s ");
        $this->getDataFromExcel(config('params.import_file_path.mst_customers.extra.path'));
        $this->start_row = 1;
        for ($row = $this->start_row; $row <= $this->highestRow; $row++) {
            if($row==1){
                //$this->numRead++;
                continue;
            }
            $rowData = $this->sheet->rangeToArray('A' . $row . ':' .  $this->highestColumn . $row, null, false, false, true);
            $mst_customers_cds[$rowData[$row]['A']] = (string)$rowData[$row]['B'];
        }
        return $mst_customers_cds;
    }

    protected function insertDB($error_fg, $row, $record){
        if (!$error_fg) {
            DB::beginTransaction();
            try {
                if (!empty($record)) {
                    DB::table('mst_customers_copy1')->insert($record);
                    DB::commit();
                    $this->numNormal++;
                }
            } catch (\Exception $e) {
                DB::rollback();
                $this->numErr++;
                $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                    "fileName" => config('params.import_file_path.mst_customers.main.fileName'),
                    "row" => $row,
                    "errorDetail" => $e->getMessage(),
                ]));
            }
        } else {
            $this->numErr++;
        }
    }
    protected function validate($record, $row, $column_name, $fileName, &$error_fg){
        if (DB::table('mst_customers_copy1')->where('mst_customers_cd', '=', $record['mst_customers_cd'])->whereNull('deleted_at')->exists()) {
            $error_fg = true;
            $this->log("DataConvert_Err_ID_Match", Lang::trans("log_import.existed_record_in_db", [
                "fileName" => $fileName,
                "fieldName" => $column_name['mst_customers_cd'],
                "row" => $row,
            ]));
        }
        if(!isset($record['bill_mst_customers_cd'])){
            $error_fg =true;
            $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                "mainFileName" => config('params.import_file_path.mst_customers.main.fileName'),
                "fieldName" => $column_name['mst_customers_cd'],
                "row" => $row,
                "extraFileName" => config('params.import_file_path.mst_customers.extra.fileName'),
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
                            "DBvalue" => substr($record[$field], 0, $error[0]),
                        ]));
                        $record[$field] = substr($record[$field], 0, $error[0]);
                    } else if ($ruleName == 'Required') {
                        $error_fg = true;
                        $this->log("DataConvert_Err_required", Lang::trans("log_import.required", [
                            "fileName" => $fileName,
                            "fieldName" => $column_name[$field],
                            "row" => $row,
                        ]));
                    }else if ($ruleName == 'KanaCustom') {
                        $error_fg = true;
                        $this->log("DataConvert_Err_KANA", Lang::trans("log_import.check_kana", [
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