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
    public $belongCompanyId=null;
    public $prefCdByPrefNameCustom=[];
    public $businessOffice=[];
    public $depend_kb_1=null;
    public $depend_kb_2=null;
    public $excel_column = [
        'A'=>'staff_cd',
        'B'=>'staff_nm',
        'C'=>'staff_nm_kana',
        'D'=>'employment_pattern_id',
        'G' => 'sex_id',
        'I'=>'birthday',
        'J'=>'enter_date',
        'K'=>'retire_date',
        'L' => 'zip_cd',
        'M'=>'address1',
        'N'=>'address2',
        'O'=>'phone_number',
        'Q'=>'staff_dependents_nm_0',
        'AA'=>'notes',
        'AB' => 'created_at',
        'AE' => 'modified_at',
        'S'=>'staff_dependents_nm_1',
        'T'=>'staff_dependents_nm_2',
        'U'=>'staff_dependents_nm_3',
        'V'=>'staff_dependents_nm_4',
        'W'=>'staff_dependents_nm_5',
        'Z'=>'mst_business_office_id',
    ];
    public $column_main_name=[
        'staff_cd'=>'社員CD',
        'employment_pattern_id'=>'社員区分',
        'staff_nm'=>'社員名',
        'last_nm'=>'社員名(姓）',
        'first_nm'=>'社員名(名）',
        'staff_nm_kana'=>'社員名かな',
        'last_nm_kana'=>'社員名かな(姓)',
        'first_nm_kana'=>'社員名かな(名)',
        'spouse_nm'=>'配偶者氏名',
        'mst_business_office_id'=>'office_id',
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
        'educational_background'=>'最終学歴',
        'educational_background_dt'=>'最終学歴日付',
        'drivers_license_number'=>'免許証番号',
        'drivers_license_issued_dt'=>'書換年月日',
        'retire_reasons'=>'退職理由',
        'death_reasons'=>'死亡理由',
        'death_dt'=>'死亡年月日'
    ];
    public $column_name_extra_file_1 = [
        'staff_cd'=> '社員番号',
    ];
    public $column_name_extra_file_2 = [
        'staff_cd'=> '社員CD',
    ];
    public $column_name_extra_file_3 = [
        'staff_cd'=> '社員ＣＤ',
    ];

    public $excel_column_insurer=[
        'staff_cd'=>'社員番号',
    ];
    public $excel_column_driver_license=[
        'staff_cd'=>'社員ＣＤ',
        'drivers_license_number'=>'免許証番号',
        'drivers_license_issued_dt'=>'書換年月日'
    ];
    public $column_staff_dependents=[
        'staff_dependents_nm_0'=>'配偶者氏名',
        'staff_dependents_nm_1'=>'扶養者１',
        'staff_dependents_nm_2'=>'扶養者２',
        'staff_dependents_nm_3'=>'扶養者３',
        'staff_dependents_nm_4'=>'扶養者４',
        'staff_dependents_nm_5'=>'扶養者５'
    ];
    public $ruleValid = [
        'staff_cd'  => 'required',
        'last_nm'  => 'nullable|length:25',
        'last_nm_kana'  => 'nullable|length:50',
        'first_nm'  => 'length:25|nullable',
        'first_nm_kana'=>'nullable|length:50',
        'address1'=>'length:200|nullable',
        'address2'=>'length:200|nullable',
        "landline_phone_number"=>"length:20|nullable",
        "cellular_phone_number"=>"length:20|nullable",
        "notes"=>"length:255|nullable",
        "insurer_number"=>"length:20|nullable",
        "basic_pension_number"=>"length:20|nullable",
        "person_insured_number"=>"length:20|nullable",
        "educational_background"=>"length:50|nullable",
        "retire_reasons"=>"length:50|nullable",
        "death_reasons"=>"length:50|nullable",
        'drivers_license_number'=>'length:12|nullable',
        "created_at"=>"required",
        "modified_at"=>"required",
    ];
    public $childFile1=[];
    public $childFile2=[];
    public $childFile3=[];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staffs.main');
        date_default_timezone_set("Asia/Tokyo");
        $this->dateTimeRun = date("YmdHis");
        $mGeneralPurposes=new MGeneralPurposes();
        $mBusinessOffices=new MBusinessOffices();
        $findBelongCompany= $mGeneralPurposes->where('data_kb','=','01004')->where('date_nm','=', "アキタ")->first();
        if($findBelongCompany)
        {
            $this->belongCompanyId=$findBelongCompany->date_id;
        }
        $this->depend_kb_1=$mGeneralPurposes->select('date_id')
            ->where('data_kb','=',config('params.data_kb.dependent_kb'))
            ->where('date_nm','LIKE','%'.'配偶者'.'%')
            ->first()['date_id'];
        $this->depend_kb_2=$mGeneralPurposes->select('date_id')
            ->where('data_kb','=',config('params.data_kb.dependent_kb'))
            ->where('date_nm','LIKE','%'.'扶養者'.'%')
            ->first()['date_id'];
        $this->businessOffice=$mBusinessOffices->getMstBusinessOffice();
        $this->prefCdByPrefNameCustom=$mGeneralPurposes->getPrefCdByPrefNameCustom();
        $this->childFile1=$this->readChildFile(config('params.import_file_path.mst_staffs.health_insurance_card_information'),'insurance');
        $this->childFile2=$this->readChildFile(config('params.import_file_path.mst_staffs.staff_background'),'staff_background');
        $this->childFile3=$this->readChildFile(config('params.import_file_path.mst_staffs.drivers_license'),'driver_license');

    }

    public function import()
    {
        $this->mainReading($this->rowCurrentData,$this->rowIndex);
    }

    public function formatDateString($date)
    {
        if(empty($date))
        {
            return null;
        }
        else {
            return \PHPExcel_Style_NumberFormat::toFormattedString($date,'yyyy-mm-dd');

        }
    }
    public function formatDateTimeString($date)
    {
        if(empty($date))
        {
            return null;
        }
        else {
            return \PHPExcel_Style_NumberFormat::toFormattedString($date, 'yyyy/mm/dd hh:mm:ss');
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
    public function readChildFile($path,$type){
        $column_insurer=[
            'A'=>'staff_cd',
            'B'=>'insurer_number',
            'C'=>'basic_pension_number',
            'D'=>'person_insured_number',
            'E'=>'health_insurance_class',
            'F'=>'welfare_annuity_class',
        ];
        $column_background=[
            'A'=>'staff_cd',
            'B'=>'educational_background',
            'C'=>'educational_background_dt',
            'AK'=>'retire_reasons',
            'AM'=>'death_reasons',
            'AL'=>'death_dt'
        ];
        $column_driver_license=[
            'B'=>'staff_cd',
            'E'=>'drivers_license_number',
            'H'=>'drivers_license_issued_dt',
        ];
        try {
            $inputFileType = \PHPExcel_IOFactory::identify($path);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($path);
        } catch(Exception $e) {
                return ('Error loading file "'.pathinfo($path,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $start_row = $this->startRow;
        $recordChild=array();
        $record=array();
        for ($row = $start_row; $row <= $highestRow; $row++) {
            $rowCurrentData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
            if($type=="insurance")
            {
                foreach ($rowCurrentData[$row] as  $pos=>$value) {
                    if(isset($column_insurer[$pos]))
                    {
                        $record[$column_insurer[$pos]] = !isset($value) ? null :(string)$value;
                        $record['row_index']=$row;
                    }
                }
                if(isset($record['staff_cd']))
                {
                    $recordChild[$record['staff_cd']]=$record;
                    unset($record['staff_cd']);
                }

            }
            elseif($type=="staff_background")
            {
                foreach ($rowCurrentData[$row] as  $pos=>$value) {
                    if (isset($column_background[$pos])) {
                        switch ($column_background[$pos]) {
                            case 'educational_background_dt':
                                $record[$column_background[$pos]] = $this->formatDateString($value);
                                break;
                            case 'death_dt':
                                $record[$column_background[$pos]] = $this->formatDateString($value);
                                break;
                            default:
                                $record[$column_background[$pos]] =  !isset($value)? null : (string)$value;
                                break;
                        }
                        $record['row_index'] = $row;
                    }
                }
                if(isset($record['staff_cd']))
                {
                    $recordChild[$record['staff_cd']]=$record;
                    unset($record['staff_cd']);
                }
            }
            elseif($type="driver_license")
            {
                foreach ($rowCurrentData[$row] as  $pos=>$value) {
                    if(isset($column_driver_license[$pos]))
                    {
                        switch ($column_driver_license[$pos]) {
                            case 'drivers_license_issued_dt':
                                $record[$column_driver_license[$pos]] = $this->formatDateString($value);
                                break;
                            default:
                                $record[$column_driver_license[$pos]] =!isset($value) ? null : (string)$value;
                                break;
                        }
                        $record['row_index']=$row;
                    }
                }
                if(isset($record['staff_cd']))
                {
                    $recordChild[$record['staff_cd']]=$record;
                    unset($record['staff_cd']);
                }
            }
        }
        unset($objPHPExcel);
        unset($objReader);
        return $recordChild;
    }
    public function mainReading($rowData,$row){
        $excel_column = $this->excel_column;
        $record = array();
        $this->error_fg=false;
        $employment_pattern_id=$rowData[$row]['D'];
        if(!empty($rowData[$row]) && $employment_pattern_id<>3)
        {
            foreach($rowData[$row] as $pos=>$value){
                if(isset($excel_column[$pos])) {
                    $record[$excel_column[$pos]] = !isset($value)? null : (string)$value;
                }
            }
            $record['modified_at'] = $this->formatDateTimeString($record['modified_at']);
            $record['created_at']=  $this->formatDateTimeString($record['created_at']);
            $record['birthday'] = $this->formatDateString($record['birthday']);
            $record['enter_date']= $this->formatDateString($record['enter_date']);
            $record['retire_date']=$this->formatDateString($record['retire_date']);
            $record['zip_cd'] = is_null($record['zip_cd'])?null:str_replace("-","",$record['zip_cd']);
            if($this->getCellularPhone($record['phone_number']))
            {
                $record['cellular_phone_number']=$this->getCellularPhone($record['phone_number']);
            }
            else
            {
                $record['landline_phone_number']=is_null($record['phone_number'])?null:$record['phone_number'];
            }
            if($record['employment_pattern_id']!= '' && (string)$record['employment_pattern_id'] != '0') {
                $data_kb=config('params.data_kb')['employment_pattern'];
                $employment_pattern_kb = config('params.import_mst_staffs_data_kb')['employment_pattern_kb'];
                if (isset($employment_pattern_kb[$record['employment_pattern_id']])) {
                    $result = $this->checkExistDataAndInsertCustom($data_kb, $employment_pattern_kb[$record['employment_pattern_id']], config('params.import_file_path.mst_staffs.main_file_name'), $this->column_main_name['employment_pattern_id'], $row);
                    if ($result == null) {
                        $this->error_fg = true;
                    }
                    $record['employment_pattern_id'] = $result;
                } else {
                    $this->error_fg = true;
                    $this->log("DataConvert_Add_general_purposes", Lang::trans("log_import.add_general_purposes_number", [
                        "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                        "fieldName" => $this->column_main_name['employment_pattern_id'],
                        "row" => $row,
                    ]));
                }
            }
            if($record['sex_id']!= '' && (string)$record['sex_id'] != '0')
            {
                $data_kb=config('params.data_kb')['sex'];
                $sex_kb = config('params.import_mst_staffs_data_kb')['sex_kb'];
                if(isset($sex_kb[$record['sex_id']])) {
                    $result = $this->checkExistDataAndInsertCustom($data_kb, $sex_kb[$record['sex_id']], config('params.import_file_path.mst_staffs.main_file_name'), $this->column_main_name['sex_id'], $row);
                    if ($result == null) {
                        $this->error_fg = true;
                    }
                    $record['sex_id'] = $result;
                }
                else
                {
                    $this->error_fg = true;
                    $this->log("DataConvert_Add_general_purposes", Lang::trans("log_import.add_general_purposes_number", [
                        "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                        "fieldName" =>  $this->column_main_name['sex_id'],
                        "row" => $row,
                    ]));
                }
            }

            $prefectures = $this->getPrefCdByPrefName($record['address1']);
            if($prefectures)
            {
               $record['prefectures_cd']=$prefectures['prefectures_cd'];
               $record['address1']=$prefectures['address1'];
            }
            $office=$this->getOfficeId($record['mst_business_office_id']);
            $record['mst_business_office_id']=isset($office)?$office:null;
            $staffName=$this->explodeStaffName($record['staff_nm'],null);
            if(!empty($staffName))
            {
                $record['first_nm']=$staffName['first_nm'];
                $record['last_nm']=$staffName['last_nm'];
            }
            unset($record['staff_nm']);
            $staffNameKana=$this->explodeStaffName($record['staff_nm_kana'],'kana');
            if(!empty($staffNameKana))
            {
                $record['first_nm_kana']=$staffNameKana['first_nm_kana'];
                $record['last_nm_kana']=$staffNameKana['last_nm_kana'];
            }
            unset($record['staff_nm_kana']);
            $data=$this->validateRow($record);
            if(!empty($data) && $this->error_fg==false)
            {
                $data['belong_company_id']=$this->belongCompanyId;
                $data['enable_fg']=true;
                $data['last_nm_kana']=!empty($data['last_nm_kana'])? Common::convertToKanaExcel($data['last_nm_kana']):null;
                $data['first_nm_kana']=!empty($data['first_nm_kana'])? Common::convertToKanaExcel($data['first_nm_kana']):null;
                unset($data['staff_nm']);
                unset($data['staff_nm_kana']);
                unset($data["phone_number"]);
                unset($data["row_index"]);
                $this->insertDB($data);
            }
            else
            {
                $this->numErr++;
            }
        }


    }
    public function getPrefCdByPrefName($address1)
    {
        if($address1!=null)
        {
            $prefCdByPrefNameCustom=$this->prefCdByPrefNameCustom;
            $arrPrefName=null;
            if(mb_strripos($address1,'県')!==false)
            {
                $pref_name=mb_substr($address1, 0, mb_strripos($address1,'県')+1);
                if(isset($prefCdByPrefNameCustom[$pref_name]))
                {
                    $pref_cd=$prefCdByPrefNameCustom[$pref_name];
                    $arrPrefName['prefectures_cd']=$pref_cd;
                    $arrPrefName['address1']=mb_substr($address1, mb_strripos($address1,'県')+1,mb_strlen($address1));
                    return $arrPrefName;
                }
                else
                {
                    return null;
                }

            }
            else
            {
                return null;
            }
        }
        else
        {
            return null;
        }

    }
    public function getOfficeId($office_cd)
    {
        if($office_cd!=null) {
            $businessOffice = $this->businessOffice;
            if (isset($businessOffice[$office_cd])) {
                $office_id = $businessOffice[$office_cd];
                return $office_id;
            }
        }
            return null ;
    }
    protected function checkExistDataAndInsertCustom($data_kb,$string,$fileName,$fieldName, $row){
        $mGeneralPurposes = new MGeneralPurposes();
        $query = $mGeneralPurposes->where('data_kb', $data_kb)
            ->where('deleted_at', '=', null);
        $result = $query->where('date_nm', $string)->first();
        if (!$result) {
            $this->log("DataConvert_Add_general_purposes", Lang::trans("log_import.add_general_purposes_number", [
                "fileName" => $fileName,
                "fieldName" => $fieldName,
                "row" => $row,
            ]));
            return null;
        } else {
            return $result->date_id;
        }
    }
    public function validateRow($record){
        if (DB::table($this->table)->where('staff_cd', '=', $record['staff_cd'])->whereNull('deleted_at')->exists()) {
            $this->error_fg = true;
            $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.existed_record_in_db",[
                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                "fieldName" => $this->column_main_name['staff_cd'],
                "row" => $this->rowIndex,
            ]));
        }
        if(isset($this->childFile1[$record['staff_cd']]))
        {
            $insurance=$this->childFile1[$record['staff_cd']];
            $record+=$this->trimFieldInChildFile($insurance,'file_nm_1');
            unset($this->childFile1[$record['staff_cd']]);
        }
        if(isset($this->childFile2[$record['staff_cd']]))
        {
            $staffBackground=$this->childFile2[$record['staff_cd']];
            $record+=$this->trimFieldInChildFile($staffBackground,'file_nm_2');
            unset($this->childFile2[$record['staff_cd']]);
        }
        if(isset($this->childFile3[$record['staff_cd']]))
        {
            $driverLicense=$this->childFile3[$record['staff_cd']];
            $record+=$this->trimFieldInChildFile($driverLicense,'file_nm_3');
            unset($this->childFile3[$record['staff_cd']]);
        }
        if( !empty($this->ruleValid)){
            $validator = Validator::make( $record,$this->ruleValid);
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
                                "DBvalue" => "null",
                            ]));
                            $record[$field] = null;
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
            }
        }
        return $record;
    }
    public function trimFieldInChildFile($recordChildFile,$filename)
    {
        $validatorChild = Validator::make($recordChildFile, $this->ruleValid);
        if ($validatorChild->fails()) {
            $failedRules = $validatorChild->failed();
            foreach ($failedRules as $field => $errors) {
                foreach ($errors as $ruleName => $error) {
                    if ($ruleName == 'Length') {
                        $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                            "fileName" => config('params.import_file_path.mst_staffs.extra_file.'.$filename),
                            "excelFieldName" => $this->column_main_name[$field],
                            "row" => $recordChildFile['row_index'],
                            "excelValue" => $recordChildFile[$field],
                            "tableName" => $this->table,
                            "DBFieldName" => $field,
                            "DBvalue" => "null",
                        ]));
                        $recordChildFile[$field] = null;
                    }
                }
            }
        }
        return $recordChildFile;
    }
    public function insertDB($data)
    {
        try{
            $staffDependents=array();
            for( $k=0; $k<=5; $k++) {
                array_push($staffDependents,$data['staff_dependents_nm_'.$k]);
                unset($data['staff_dependents_nm_'.$k]);
            }
            $password=!empty($data["birthday"])?str_replace("-","",$data["birthday"]):config('params.import_file_path.mst_staffs.password_default');
            $data["password"]=bcrypt($password);
            $id = DB::table($this->table)->insertGetId( $data);
            $this->numNormal++;
            if($id)
             {
                 $objPHPExcel=$this->objPHPExcel;
                 $objPHPExcel->setActiveSheetIndex(0);
                 $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $this->rowIndex, $this->error_fg==true?"":$password);
                 $configArr=[
                         'mst_staff_id'=>$id,
                         'created_at'=>$data['created_at'],
                         'modified_at'=>$data['modified_at']
                 ];
                  $this->insertMstStaffDependents($configArr,$staffDependents,$data['last_nm']);
             };
        }catch (\Exception $e){
            $this->numErr++;
            $this->error_fg=true;
            $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                "row" => $this->rowIndex,
                "errorDetail" => $e->getMessage(),
            ]));
        }
    }
    public function getDependentKB($type)
    {
        $result=$type==0?$this->depend_kb_1:$this->depend_kb_2;
        return $result;
    }
    public function trimStaffDependents($value)
    {
        if(mb_strripos($value,"(")>0)
        {
            $value=mb_substr($value,0,mb_strripos($value,"("));
        }
        if(strpos($value,"（"))
        {
            $value=mb_substr( $value,0,mb_strripos($value,"（"));
        }
        return $value;
    }
    public function insertMstStaffDependents($configArr,$staffDependents,$last_nm)
    {
        try{
            $arrInsert=array();
            $record=$configArr;
            for($k=0;$k<=count($staffDependents);$k++)
            {
                if(isset($staffDependents[$k]) && !empty($staffDependents[$k]))
                {
                        $staff_dependents_nm=$this->explodeStaffName($staffDependents[$k],null);
                        if (mb_strlen($staff_dependents_nm['first_nm'], 'UTF-8')>25) {
                                $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                                    "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                                    "excelFieldName" => $this->column_staff_dependents['staff_dependents_nm_'.$k],
                                    "row" => $this->rowIndex,
                                    "excelValue" => $staff_dependents_nm['first_nm'],
                                    "tableName" => 'mst_staff_dependents',
                                    "DBFieldName" => 'first_nm',
                                    "DBvalue" => "null",
                                ]));
                                $staff_dependents_nm['first_nm'] = null;
                        }
                        if (mb_strlen($staff_dependents_nm['last_nm'], 'UTF-8')>25) {
                            $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                                "excelFieldName" => $this->column_staff_dependents['staff_dependents_nm_'.$k],
                                "row" => $this->rowIndex,
                                "excelValue" => $staff_dependents_nm['last_nm'],
                                "tableName" => 'mst_staff_dependents',
                                "DBFieldName" =>'last_nm',
                                "DBvalue" => "null",
                            ]));
                            $staff_dependents_nm['last_nm'] =null;
                        }
                        $staffDependents[$k]=$staff_dependents_nm;
                        $record['dependent_kb']=$this->getDependentKB($k);
                        if(is_null($staffDependents[$k]["first_nm"]))
                        {
                            $first_nm_dependent= $staffDependents[$k]["last_nm"];
                            $last_nm_dependent=$last_nm;
                        }
                        else
                        {
                            $first_nm_dependent= $staffDependents[$k]["first_nm"];
                            $last_nm_dependent=$staffDependents[$k]["last_nm"];
                        }
                        $record['last_nm']=$this->trimStaffDependents($last_nm_dependent);
                        $record['first_nm']=$this->trimStaffDependents($first_nm_dependent);
                        $arrInsert[]=$record;

                }
            }
            DB::table('mst_staff_dependents')->insert($arrInsert);
        }
        catch (\Exception $e)
        {
            $this->error_fg=true;
            $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                "row" => $this->rowIndex,
                "errorDetail" => $e->getMessage(),
            ]));
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
    protected function checkIDErrorMatch()
    {
        for ($k = 1; $k <=3; $k++){
            if(count($this->{"childFile".$k}) > 0){
                foreach ($this->{"childFile".$k} as $key=>$item){
                    if(isset($key))
                    {
                        $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.no_record_in_extra_file",[
                            "mainFileName" => config('params.import_file_path.mst_staffs.extra_file.file_nm_'.$k),
                            "fieldName" => $this->{'column_name_extra_file_'.$k}['staff_cd'],
                            "row" => $item['row_index'],
                            "extraFileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                        ]));

                    }
                }
            }
        }
    }
}
