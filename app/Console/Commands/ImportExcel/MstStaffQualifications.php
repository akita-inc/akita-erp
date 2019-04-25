<?php

namespace App\Console\Commands\ImportExcel;
use App\Models\MBusinessOffices;
use App\Models\MGeneralPurposes;
use App\Models\MStaffQualifications;
use App\Models\MStaffs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class MstStaffQualifications extends BaseImport{
    public $path = "";
    public $excel_column = [
        'A'=>'mst_staff_id',
        'D'=>'qualification_kind_id',
        'E'=>'qualification_kind_id',
        'F'=>'qualification_kind_id',
        'G'=>'qualification_kind_id',
        'H'=>'qualification_kind_id',
        'AC'=>'qualification_kind_id',
        'AF'=>'qualification_kind_id',
        'N' => 'acquisition_dt',
        'O' => 'acquisition_dt',
        'P' => 'acquisition_dt',
        'Q' => 'acquisition_dt',
        'R' => 'acquisition_dt',
        'AD' => 'acquisition_dt',
        'AG' => 'acquisition_dt',
        'AE' => 'amounts',
        'AH' => 'amounts',
    ];
    public $column_name=[
        'A'=>'社員CD',
        '1'=>'資格１',
        '2'=>'資格２',
        '3'=>'資格３',
        '4'=>'資格４',
        '5'=>'資格５',
        '6'=>'会社取得１',
        '7'=>'会社取得２',
        'N' => '資格１日付',
        'O' => '資格２日付',
        'P' => '資格３日付',
        'Q' => '資格４日付',
        'R' => '資格５日付',
        'AD' => '会社取得日付１',
        'AG' => '会社取得日付２',
        'amounts_6' => '会社取得金額１',
        'amounts_7' => '会社取得金額２',

    ];
    public $labels=[];
    public $messagesCustom=[];
    public $rules = [
        'mst_staff_id'  => 'required',
        'qualification_kind_id'  => 'nullable|length:5',
        'amounts'  => 'nullable|length:11',
        "created_at"=>"required",
        "modified_at"=>"required",
    ];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staff_qualifications.main.path');
        date_default_timezone_set("Asia/Tokyo");
        $this->dateTimeRun = date("YmdHis");
    }

    public function import()
    {
        $error_fg=false;
        $currentTime = date("Y/m/d H:i:s");
        $excel_column = $this->excel_column;
        $record = array();
        $row = $this->rowIndex;
        $rowData = $this->rowCurrentData;
        $listQualifications = [];
        $mGeneralPurposes = new MGeneralPurposes();
        $mst_staff_id = '';
        if(!empty($rowData[$row]))
        {
            foreach($rowData[$row] as $pos=>$value){
                if(isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]){
                        case 'mst_staff_id':
                            if(!is_null($value)){
                                $findStaff = MStaffs::query()->where('staff_cd',(string)$value)->whereNull('deleted_at')->first();
                                if($findStaff){
                                    $mst_staff_id = (string)$findStaff->id;
                                }else{
                                    $error_fg = true;
                                    $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                                        "mainFileName" => config('params.import_file_path.mst_staff_qualifications.main.fileName'),
                                        "fieldName" => $this->column_name[$pos],
                                        "row" => $row,
                                        "extraFileName" =>'mst_staffs',
                                    ]));
                                }
                            }else{
                                $error_fg = true;
                                $this->log("DataConvert_Err_required", Lang::trans("log_import.required", [
                                    "fileName" => config('params.import_file_path.mst_staff_qualifications.main.fileName'),
                                    "fieldName" => $this->column_name[$pos],
                                    "row" => $row,
                                ]));
                            }
                            break;
                        case 'amounts':
                            $index = '';
                            switch ($pos){
                                case 'AE':
                                    $index = 6;break;
                                case 'AH':
                                    $index = 7;break;
                            }

                            if(!is_null($value)){
                                $listQualifications[$index][$excel_column[$pos]] = $value;
                            }
                            break;
                        case 'acquisition_dt':
                            $index = '';
                            switch ($pos){
                                case 'N':
                                    $index = 1;break;
                                case 'O':
                                    $index = 2;break;
                                case 'P':
                                    $index = 3;break;
                                case 'Q':
                                    $index = 4;break;
                                case 'R':
                                    $index = 5;break;
                                case 'AD':
                                    $index = 6;break;
                                case 'AG':
                                    $index = 7;break;
                            }
                            $listQualifications[$index][$excel_column[$pos]]  = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'yyyy/mm/dd');
                            break;
                        case 'qualification_kind_id':
                            $index = '';
                            switch ($pos){
                                case 'D':
                                    $index = 1;break;
                                case 'E':
                                    $index = 2;break;
                                case 'F':
                                    $index = 3;break;
                                case 'G':
                                    $index = 4;break;
                                case 'H':
                                    $index = 5;break;
                                case 'AC':
                                    $index = 6;break;
                                case 'AF':
                                    $index = 7;break;
                            }

                            $listQualifications[$index][$excel_column[$pos]] = (string)$value;
                            break;
                    }
                }

            }
            if(!empty($mst_staff_id)){
                foreach ($listQualifications as $key => $item){
                    if(!empty($item['qualification_kind_id'])){
                        $data_kb = config('params.data_kb')['qualification_kind'];
                        $result = $this->checkExistDataAndInsert($data_kb, $item['qualification_kind_id'],config('params.import_file_path.mst_staff_qualifications.main.fileName'),$this->column_name[$key], $row );
                        $item["qualification_kind_id"] = $result;
                        $item["mst_staff_id"] = $mst_staff_id;
                        $item["created_at"] = $currentTime;
                        $item["modified_at"] = $currentTime;
                        $this->validate($item,$row, $this->column_name, config('params.import_file_path.mst_staff_qualifications.main.fileName'),$key,$error_fg);

                        $this->insertDB($error_fg, $row, $item);

                    }
                }
            }else{
                $this->numErr++;
            }

        }
    }

    protected function validate(&$record, $row, $column_name, $fileName,$key, &$error_fg){

        $validator = Validator::make($record, $this->rules);

        if ($validator->fails()) {
            $failedRules = $validator->failed();
            foreach ($failedRules as $field => $errors) {
                foreach ($errors as $ruleName => $error) {
                    if ($ruleName == 'Length') {
                        $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                            "fileName" => $fileName,
                            "excelFieldName" => $column_name[$field.'_'.$key],
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
                    }
                }
            }
        }
    }

    protected function insertDB($error_fg, $row, $record){
        if (!$error_fg) {
            DB::beginTransaction();
            try {
                if (!empty($record)) {
                    DB::table('mst_staff_qualifications')->insert($record);
                    DB::commit();
                    $this->numNormal++;
                }
            } catch (\Exception $e) {
                DB::rollback();
                $this->numErr++;
                $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                    "fileName" => config('params.import_file_path.mst_staff_qualifications.main.fileName'),
                    "row" => $row,
                    "errorDetail" => $e->getMessage(),
                ]));
            }
        } else {
            $this->numErr++;
        }
    }
}