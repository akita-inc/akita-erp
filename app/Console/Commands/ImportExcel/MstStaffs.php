<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */

use App\Helpers\Common;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MStaffs;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\DB;
use App\Models\MBusinessOffices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
class MstStaffs extends BaseImport
{
    public $path = "";
    public $error_fg=false;
    public $excel_column = [
        'A'=>'staff_cd',
        'B'=>'staff_nm',
        'C'=>'staff_nm_kana',
        'D'=>'employment_pattern_id',
        'E'=>'mst_business_office_id',
        'G' => 'sex_id',
        'I'=>'birthday',
        'J'=>'enter_date',
        'K'=>'retire_date',
        'L' => 'zip_cd',
        'M'=>'address1',
        'N'=>'address2',
        'O'=>'phone_number',
        'AA'=>'notes',
        'AB' => 'created_at',
        'AE' => 'modified_at',
        'S'=>'staff_dependents_nm_S',
        'T'=>'staff_dependents_nm_T',
        'U'=>'staff_dependents_nm_U',
        'V'=>'staff_dependents_nm_V',
        'W'=>'staff_dependents_nm_W'
    ];
    public $column_main_name=[
        'staff_cd'=>'社員CD',
        'employment_pattern_id'=>'社員区分',
        'staff_nm'=>'社員名',
        'staff_nm_kana'=>'社員名かな',
        'mst_business_office_id'=>'社員所属CD',
        'sex_id' => '性別',
        'birthday'=>'生年月日',
        'enter_date'=>'入社年月日',
        'retire_date'=>'退社年月日',
        'zip_cd' => '郵便番号',
        'address1'=>'住所１',
        'address2'=>'住所２',
        'phone_number'=>'電話番号',
        'notes'=>'備考',
        'created_at' => '登録日',
        'modified_at' => '最終更新日',
        'insurer_number'=>'保険番号',
        'landline_phone_number'=>'電話番号',
        'basic_pension_number'=>'基礎年金番号',
        'person_insured_number'=>'被保険者番号',
        'health_insurance_class'=>'健康保険等級',
        'welfare_annuity_class'=>'厚生年金等級',
        'relocation_municipal_office_cd'=>'市町村役場コード',
        'educational_background'=>'最終学歴',
        'educational_background_dt'=>'最終学歴日付',
        'drivers_license_number'=>'免許証番号',
        'drivers_license_issued_dt'=>'書換年月日',
        'retire_reasons'=>'退職理由',
        'death_reasons'=>'死亡理由',
        'death_dt'=>'死亡年月日'
    ];
    public $excel_column_insurer=[
        'staff_cd'=>'社員番号',
    ];
    public $excel_column_driver_license=[
        'staff_cd'=>'社員ＣＤ',
        'drivers_license_number'=>'免許証番号',
        'drivers_license_issued_dt'=>'書換年月日'
    ];
    public $ruleValid = [
        'staff_cd'  => 'required|length:5|unique:mst_staffs,staff_cd',
        'last_nm'  => 'nullable|length:25',
        'last_nm_kana'  => 'kana_custom|nullable|length:50',
        'first_nm'  => 'length:25|nullable',
        'first_nm_kana'=>'kana_custom|nullable|length:50',
        'zip_cd'=>'nullable|length:7',
        'address1'=>'length:20|nullable',
        'address2'=>'length:20|nullable',
        "landline_phone_number"=>"length:20|nullable",
        "cellular_phone_number"=>"length:20|nullable",
        "notes"=>"length:50|nullable",
        "insurer_number"=>"length:20|nullable",
        "health_insurance_class"=>"length:10|nullable",
        "welfare_annuity_class"=>"length:10|nullable",
        "relocation_municipal_office_cd"=>"nullable|length:6",
        "basic_pension_number"=>"length:20|nullable",
        "person_insured_number"=>"length:20|nullable",
        "educational_background"=>"length:50|nullable",
        "retire_reasons"=>"length:50|nullable",
        "death_reasons"=>"length:50|nullable",
        'drivers_license_number'=>'length:12|nullable',
        'drivers_license_issued_dt'=>'nullable',
        "created_at"=>"required",
        "modified_at"=>"required",
    ];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staffs.main');
        date_default_timezone_set("Asia/Tokyo");
        $this->dateTimeRun = date("YmdHis");
    }

    public function import()
    {
        $this->mainReading($this->rowCurrentData,$this->rowIndex);
    }
    public function formatDateString($date)
    {
        return \PHPExcel_Style_NumberFormat::toFormattedString($date,'yyyy-mm-dd');
    }
    public function formatDateTimeString($date)
    {
        return \PHPExcel_Style_NumberFormat::toFormattedString($date,'yyyy/mm/dd hh:mm:ss');
    }
    public function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        //add password cell
        $objPHPExcel=$this->objPHPExcel;
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $this->rowIndex, $randomString);
            return $randomString;

    }
    public function getBelongCompanyId()
    {
        $mGeneralPurposes =MGeneralPurposes::select('date_id')
            ->where('data_kb','=','01004')
            ->where('date_nm','=', "アキタ")
            ->first();
        if($mGeneralPurposes)
        {
            return $mGeneralPurposes['date_id'];
        }
        else
        {
            return null;
        }

    }
    public function getOfficeId($office_cd)
    {
        $mBusinessOffice=new MBusinessOffices();
        $result=$mBusinessOffice->getMstBusinessOfficeId($office_cd);
        if(!empty($result['id']))
        {
            return $result['id'];
        }
        else
        {
            return null;
        }
    }
    public function getCellularPhone($phone)
    {
        $cellPhone=substr($phone,0,3);
        if($cellPhone=="090" || $cellPhone=="080" || $cellPhone=="070")
        {
            return $phone;
        }
        return null;
    }
    public function getSpaceBetweenName($name)
    {
        if(strpos($name, ' ') !== false)
        {
            $space=' ';
        }
        elseif(strpos($name, '　') !== false)
        {
            $space='　';
        }
        elseif (strpos($name, '  ') !== false)
        {
            $space='  ';
        }
        elseif(strpos($name, '　  ') !== false)
        {
            $space='　  ';
        }
        else
        {
            $space=' ';

        }
        return $space;
    }
    public function explodeStaffName($value,$type)
    {
        $result=array();
        $staff_nm=explode($this->getSpaceBetweenName($value),$value);
        if(count($staff_nm)>2)
        {
            $staff_nm[1]=$staff_nm[count($staff_nm)-1];
        }
        if($type=="kana")
        {
            $result['last_nm_kana']=!empty($staff_nm[0])?$staff_nm[0]:null;
            $result['first_nm_kana']=!empty($staff_nm[1])?$staff_nm[1]:null;
        }
        else
        {
            $result['last_nm']=!empty($staff_nm[0])?$staff_nm[0]:null;
            $result['first_nm']=!empty($staff_nm[1])?$staff_nm[1]:null;
        }
        return $result;
    }
    public function getDataFromChildFile($path,$type)
    {
        try {
            $column=$this->column_main_name;
            $excel_column_driver_license=$this->excel_column_driver_license;
            $data = Excel::load($path)->get();
            if($data->count()){
                if($type=="insurance")
                {
                    foreach ($data as  $key=>$value) {
                        $arr[$value->{$this->excel_column_insurer['staff_cd']}] = [
                            'insurer_number'=>$value->{$column['insurer_number']},
                            'basic_pension_number'=>$value->{$column['basic_pension_number']},
                            'person_insured_number'=>$value->{$column['person_insured_number']},
                            'health_insurance_class'=>$value->{$column['health_insurance_class']},
                            'welfare_annuity_class'=>$value->{$column['welfare_annuity_class']},
                            'relocation_municipal_office_cd'=>  $value->{$column['relocation_municipal_office_cd']},
                            'row_index'=>$key+2
                        ];
                    }
                }
                elseif($type=="staff_background")
                {
                    foreach ($data as  $key=>$value) {
                        $arr[$value->{$column['staff_cd']}] = [
                            'educational_background'=>$value->{$column['educational_background']},
                            'educational_background_dt'=> $this->formatDateString($value->{$column['educational_background_dt']}),
                            'retire_reasons'=>$value->{$column['retire_reasons']},
                            'death_reasons'=>$value->{$column['death_reasons']},
                            'death_dt'=>$this->formatDateString($value->{$column['death_dt']}),
                            'row_index'=>$key+2
                        ];
                    }
                }
                elseif($type="driver_license")
                {
                    foreach ($data as  $key=>$value) {
                        $arr[$value->{$excel_column_driver_license['staff_cd']}] = [
                            'drivers_license_number'=>$value->{$excel_column_driver_license['drivers_license_number']},
                            'drivers_license_issued_dt'=> $this->formatDateString($value->{$excel_column_driver_license['drivers_license_issued_dt']}),
                            'row_index'=>$key+2
                        ];
                    }
                }
                return $arr;
            }
        } catch(\Exception $e) {
            return null;
        }
    }
    public function mainReading($rowData,$row){
        $excel_column = $this->excel_column;
        $record = array();
        $recordStaffDepents=array();
        $mGeneralPurposes = new MGeneralPurposes();
        $insuranceArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.health_insurance_card_information'),'insurance');
        $backgroundArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.staff_background'),'staff_background');
        $driverLicenseArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.drivers_license'),'driver_license');
        $employment_pattern_id=$rowData[$row]['D'];
        if(!empty($rowData[$row]) && $employment_pattern_id<>3)
        {
            foreach($rowData[$row] as $pos=>$value){
                if(isset($excel_column[$pos])) {
                    switch ($excel_column[$pos]){
                        case 'staff_cd':
                            if(!empty($insuranceArr[$value]))
                            {
                                $insurance=$insuranceArr[$value];
                                $record['insurance']=$insurance;
                            }
                            if(!empty($backgroundArr[$value]))
                            {
                                $staff_background=$backgroundArr[$value];
                                $record['staff_background']=$staff_background;
                            }
                            if(!empty($driverLicenseArr[$value]))
                            {
                                $driverLicenseArr=$driverLicenseArr[$value];
                                $record['driver_license']=$driverLicenseArr;
                            }
                            $record[$excel_column[$pos]] = empty($value)?null:(string)$value;
                            break;
                        case 'modified_at':
                            $record[$excel_column[$pos]] = $this->formatDateTimeString($value);
                            break;
                        case 'created_at':
                            $record[$excel_column[$pos]] = $this->formatDateTimeString($value);
                            break;
                        case 'birthday':
                            $record[$excel_column[$pos]] = $this->formatDateString($value);
                            break;
                        case 'enter_date':
                            $record[$excel_column[$pos]] = $this->formatDateString($value);
                            break;
                        case 'retire_date':
                            $record[$excel_column[$pos]] = $this->formatDateString($value);
                            break;
                        case 'staff_nm':
                            $record+=$this->explodeStaffName($value,null);
                            break;
                        case 'staff_nm_kana':
                            $record+=$this->explodeStaffName($value,'kana');
                            break;
                        case 'zip_cd':
                            $record[$excel_column[$pos]] = is_null($value)?null:str_replace("-","",$value);
                            break;
                        case 'address1':
                            $prefectures_cd = $mGeneralPurposes->getPrefCdByPrefName($value);
                            if($prefectures_cd)
                            {
                                $record['prefectures_cd']=$prefectures_cd['date_id'];
                                $record[$excel_column[$pos]]=mb_substr($value,mb_strlen($prefectures_cd['date_nm']));
                            }
                            else
                            {
                                $record[$excel_column[$pos]]=$value;
                            }
                            break;
                        case 'phone_number':
                                if($this->getCellularPhone($value))
                                {
                                    $record['cellular_phone_number']=$this->getCellularPhone($value);
                                }
                                else
                                {
                                    $record['landline_phone_number']=is_null($value)?null:$value;
                                }
                            break;
                        case 'mst_business_office_id':
                            $record[$excel_column[$pos]] = $this->getOfficeId($value);
                            break;
                        case 'employment_pattern_id':
                            if($value)
                            {
                                $data_kb = config('params.data_kb')['employment_pattern'];
                                $result = $this->checkExistDataAndInsert($data_kb, $value,config('params.import_file_path.mst_staffs.main_file_name'),$this->column_main_name['employment_pattern_id'], $row );
                                if($result)
                                {
                                    $record[$excel_column[$pos]] =$result;
                                }
                            }
                            else
                            {
                                $record[$excel_column[$pos]] =null;
                            }
                            break;
                        case 'sex_id':
                            $data_kb = config('params.data_kb')['sex'];
                            $result = $this->checkExistDataAndInsert($data_kb, $value,config('params.import_file_path.mst_staffs.main_file_name'),$this->column_main_name['sex_id'], $row );
                            if ($result) {
                                $record[$excel_column[$pos]] =is_null($value)?null:(string)$value;
                            }
                            break;
                        default:
                            $record[$excel_column[$pos]] = is_null($value)?null:(string)$value;
                            break;
                    }
                }
                if(isset($excel_column[$pos])  && strpos($excel_column[$pos], 'staff_dependents_nm') !== false)
                {
                    if(!empty($value))
                    {
                        $recordStaffDepents[]=$record['staff_dependents_nm_'.$pos];
                    }
                    unset($record['staff_dependents_nm_'.$pos]);
                }
                $record["password"]=bcrypt($this->generateRandomString(8));
                $record['belong_company_id']=$this->getBelongCompanyId();
                $record['enable_fg']=true;
            }
            if(isset($record['relocation_municipal_office_cd']))
            {
                $data_kb_relocation = config('params.data_kb')['relocation_municipal_office_cd'];
                $result_relocation = $this->checkExistDataAndInsert(
                    $data_kb_relocation,
                    $record['relocation_municipal_office_cd'],
                    config('params.import_file_path.mst_staffs.main_file_name'),
                    $this->column_main_name['relocation_municipal_office_cd'],
                    $this->rowIndex );
                if($result_relocation)
                {
                    $record['relocation_municipal_office_cd']=(string)$result_relocation;
                }
            }
            if(!empty($record))
            {
                $record=$this->validateRow($record);
            }
            if(!empty($record) && $this->error_fg==false)
            {
                $record['last_nm_kana']=!empty($record['last_nm_kana'])? mb_convert_kana($record['last_nm_kana'],'KVC'):null;
                $record['first_nm_kana']=!empty($record['first_nm_kana'])? mb_convert_kana($record['first_nm_kana'],'KVC'):null;
                $record+=$record['insurance'];
                unset($record['insurance']);
                $record+=$record['staff_background'];
                unset($record['staff_background']);
                $record+=$record['driver_license'];
                unset($record['driver_license']);
                unset($record['row_index']);
                unset($record['staff_nm']);
                unset($record['staff_nm_kana']);
                unset($record["phone_number"]);
                $record['staff_dependents']=$recordStaffDepents;
                $this->insertDB($record);
            }
            else
            {
                $this->numErr++;
            }
        }


    }
    public function validateRow($record){
        $this->error_fg=false;
        if( !empty($this->ruleValid)){
            if(isset($record["driver_license"]))
            {
                $record["driver_license"]=$this->validateChildFile($record['driver_license'],'drivers_license_nm');
            }
            else
            {
                $this->error_fg=true;
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                    "mainFileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                    "fieldName" => $this->column_main_name['staff_cd'],
                    "row" => $this->rowIndex,
                    "extraFileName" => config('params.import_file_path.mst_staffs.drivers_license_nm'),
                ]));
            }

            if(isset($record['insurance']))
            {
                $record['insurance']=$this->validateChildFile($record['insurance'],'health_insurance_card_information_nm');
            }
            else
            {
                $this->error_fg=true;
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                    "mainFileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                    "fieldName" => $this->column_main_name["staff_cd"],
                    "row" => $this->rowIndex,
                    "extraFileName" => config('params.import_file_path.mst_staffs.health_insurance_card_information_nm'),
                ]));
            }

            if(isset($record['staff_background']))
            {
                $record['staff_background']=$this->validateChildFile($record['staff_background'],'staff_background_nm');
            }
            else
            {
                $this->error_fg=true;
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                    "mainFileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                    "fieldName" => $this->column_main_name['staff_cd'],
                    "row" => $this->rowIndex,
                    "extraFileName" => config('params.import_file_path.mst_staffs.staff_background_nm'),
                ]));
            }
            $validator = Validator::make( $record, $this->ruleValid );
            if ($validator->fails()) {
                    $failedRules = $validator->failed();
                    foreach ($failedRules as $field => $errors){
                        foreach ($errors as $ruleName => $error) {
                            if ($ruleName == 'Length') {
                                $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                                    "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                                    "excelFieldName" => $this->column_main_name[$field],
                                    "row" => $this->rowIndex,
                                    "excelValue" => $record[$field],
                                    "tableName" => $this->table,
                                    "DBFieldName" => $field,
                                    "DBvalue" => substr($record[$field], 0, $error[0]),
                                ]));
                                $record[$field] = substr($record[$field], 0, $error[0]);
                            };
                            if($ruleName == 'Required')
                            {
                                $this->error_fg = true;
                                    $this->log("DataConvert_Err_required", Lang::trans("log_import.required", [
                                        "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                                        "fieldName" => $this->column_main_name[$field],
                                        "row" => $this->rowIndex,
                                    ]));
                            };

                        }
                    }
                    if(isset($failedRules['last_nm_kana']['KanaCustom']) || isset($failedRules['first_nm_kana']['KanaCustom']) )
                    {
                        $this->error_fg=true;
                        $this->log("DataConvert_Err_KANA",Lang::trans("log_import.check_kana",[
                            "fileName" =>  config('params.import_file_path.mst_staffs.main_file_name'),
                            "fieldName" => $this->column_main_name['staff_nm_kana'],
                            "row" => $this->rowIndex,
                        ]));
                    }
                    if (isset($failedRules['staff_cd']['Unique'])) {
                        $this->error_fg=true;
                        $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.unique_cd",[
                            "fileName" =>  config('params.import_file_path.mst_staffs.main_file_name'),
                            "fieldName" => $this->column_main_name['staff_cd'],
                            "row" => $this->rowIndex,
                        ]));
                    }
            }
            if(is_null($record['mst_business_office_id']))
            {
                $this->error_fg=true;
                $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                    "mainFileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                    "fieldName" => $this->column_main_name["mst_business_office_id"],
                    "row" => $this->rowIndex,
                    "extraFileName" => 'mst_business_offices',
                ]));
            }

        }
        return $record;
    }
    public function validateChildFile($recordChildFile,$filename)
    {
        $validatorChild = Validator::make( $recordChildFile, $this->ruleValid);
        if ($validatorChild->fails()) {
            $failedRules = $validatorChild->failed();
            foreach ($failedRules as $field => $errors) {
                foreach ($errors as $ruleName => $error) {
                    if ($ruleName == 'Length') {
                        $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                            "fileName" => config('params.import_file_path.mst_staffs.'.$filename),
                            "excelFieldName" => $this->column_main_name[$field],
                            "row" => $recordChildFile['row_index'],
                            "excelValue" => $recordChildFile[$field],
                            "tableName" => $this->table,
                            "DBFieldName" => $field,
                            "DBvalue" => substr($recordChildFile[$field], 0, $error[0]),
                        ]));
                        $recordChildFile[$field] = substr($recordChildFile[$field],0,$error[0]);
                    }
                }
            }
        }
        return $recordChildFile;
    }
    public function insertDB($record)
    {
        DB::beginTransaction();
        try{
            $recordStaffDependents=$record['staff_dependents'];
            unset($record['staff_dependents']);
            $id = DB::table($this->table)->insertGetId( $record );
            if($id)
             {
                 $this->numNormal++;
                 DB::commit();
                 if(count($recordStaffDependents)>0)
                 {
                     $this->insertMstStaffDependents($id,$recordStaffDependents);
                 }
             };
        }catch (\Exception $e){
            $this->numErr++;
            DB::rollback();
            $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                "row" => $this->rowIndex,
                "errorDetail" => $e->getMessage(),
            ]));
        }
    }
    public function insertMstStaffDependents($mst_staff_id,$recordStaffDependents)
    {

        DB::beginTransaction();
        try{
            $staffDependents=$recordStaffDependents;
            $arrInsert=array();
             foreach ($staffDependents as $key=>$value )
             {
                 $staff_nm=$this->explodeStaffName($value,null);
                 $arrInsert[]=[
                        'mst_staff_id'=>$mst_staff_id,
                        'last_nm'=>empty($staff_nm['last_nm'])?null:$staff_nm['last_nm'],
                        'first_nm'=>empty($staff_nm["first_nm"])?null:$staff_nm["first_nm"],
                    ];
             }
             if(DB::table('mst_staff_dependents')->insert($arrInsert))
             {
                    DB::commit();
             }
        }
        catch (\Exception $e)
        {
            $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                "fileName" => config('params.import_file_path.mst_staff_dependents'),
                "row" => $this->rowIndex,
                "errorDetail" => $e->getMessage(),
            ]));
            DB::rollBack();
            return;
        }
    }
    protected function exportPassword()
    {
        $dateTimeRun=$this->dateTimeRun;
        $objPHPExcel=$this->objPHPExcel;
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, 1,'ログインパスワード');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $file=str_replace("yyyymmddhh24miss",$dateTimeRun,config('params.import_file_path.mst_staffs.export_password_file_nm'));
        $objWriter->save($file);
    }
}
