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
    public $password_random="";
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
        'basic_pension_number'=>'基礎年金番号',
        'person_insured_number'=>'被保険者番号',
        'health_insurance_class'=>'健康保険等級',
        'welfare_annuity_class'=>'厚生年金等級',
        'relocation_municipal_office_cd'=>'市町村役場コード',
        'educational_background'=>'最終学歴',
        'educational_background_dt'=>'最終学歴日付',
        'retire_reasons'=>'退職理由',
        'death_reasons'=>'死亡理由',
        'death_dt'=>'死亡年月日'
    ];
    public $excel_column_insurer=[
        'staff_cd'=>'社員番号',
    ];
    public $labels=[];
    public $messagesCustom=[];
    public $ruleValid = [
        'staff_cd'  => 'required|one_bytes_string|length:5|unique:mst_staffs,staff_cd,NULL,id,deleted_at,NULL',
        'last_nm'  => 'nullable|length:25',
        'last_nm_kana'  => 'kana|nullable|length:50',
        'first_nm'  => 'length:25|nullable',
        'first_nm_kana'=>'kana|nullable|length:50',
        'zip_cd'=>'zip_code|nullable|length:7',
        'address1'=>'length:20|nullable',
        'address2'=>'length:20|nullable',
        "landline_phone_number"=>"length:20|nullable|phone_number",
        "cellular_phone_number"=>"length:20|nullable|phone_number",
        "notes"=>"length:50|nullable",
        "insurer_number"=>"length:3|nullable",
        "health_insurance_class"=>"one_byte_number|length:10|number_range|nullable",
        "welfare_annuity_class"=>"one_byte_number|length:10|number_range|nullable",
        "relocation_municipal_office_cd"=>"nullable|length:6",
        "basic_pension_number"=>"length:11|nullable",
        "person_insured_number"=>"length:11|nullable",
        "educational_background"=>"length:50|nullable",
        "retire_reasons"=>"length:50|nullable",
        "death_reasons"=>"length:50|nullable",
    ];

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staffs.main');
    }

    public function import()
    {
        $this->numRead++;
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
        $this->password_random=$randomString;
            return $randomString;

    }
    public function getOfficeId($office_cd)
    {
        $mBusinessOffice=new MBusinessOffices();
        $result=$mBusinessOffice->getMstBusinessOfficeId($office_cd);
        if(!empty($result))
        {
            return $result;
        }
        return null;
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


        if($type=="kana")
        {
            $staff_nm=explode($this->getSpaceBetweenName($value),$value);
            $result['last_nm_kana']=isset($staff_nm[0])?$staff_nm[0]:null;
            $result['first_nm_kana']=isset($staff_nm[1])?$staff_nm[1]:null;
        }
        else
        {

            $staff_nm=explode($this->getSpaceBetweenName($value),$value);
            $result['last_nm']=isset($staff_nm[0])?$staff_nm[0]:null;
            $result['first_nm']=isset($staff_nm[1])?$staff_nm[1]:null;
        }
        return $result;
    }
    public function getDataFromChildFile($path,$type)
    {
        try {
            $column=$this->column_main_name;
            $data = Excel::load($path)->get();
            if($data->count()){
                if($type=="insurance")
                {
                    foreach ($data as  $value) {
                        $arr[$value->{$this->excel_column_insurer['staff_cd']}] = [
                            'insurer_number'=>$value->{$column['insurer_number']},
                            'basic_pension_number'=>$value->{$column['basic_pension_number']},
                            'person_insured_number'=>$value->{$column['person_insured_number']},
                            'health_insurance_class'=>$value->{$column['health_insurance_class']},
                            'welfare_annuity_class'=>$value->{$column['welfare_annuity_class']},
                            'relocation_municipal_office_cd'=>$value->{$column['relocation_municipal_office_cd']},
                        ];
                    }
                }
                elseif($type=="staff_background")
                {
                    foreach ($data as  $value) {
                        $arr[$value->{$column['staff_cd']}] = [
                            'educational_background'=>$value->{$column['educational_background']},
                            'educational_background_dt'=> $this->formatDateString($value->{$column['educational_background_dt']}),
                            'retire_reasons'=>$value->{$column['retire_reasons']},
                            'death_reasons'=>$value->{$column['death_reasons']},
                            'death_dt'=>$this->formatDateString($value->{$column['death_dt']}),
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
        $mGeneralPurposes = new MGeneralPurposes();
        $insuranceArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.health_insurance_card_information'),'insurance');
        $backgroundArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.staff_background'),'staff_background');
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
                                $record+=$insurance;
                            }
                            if(!empty($backgroundArr[$value]))
                            {
                                $staff_background=$backgroundArr[$value];
                                $record+=$staff_background;
                            }
                            $record[$excel_column[$pos]] = (string)$value;
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
                            $record+=$this->explodeStaffName(mb_convert_kana($value),'kana');
                            break;
                        case 'zip_cd':
                            $record[$excel_column[$pos]] = str_replace("-","",$value);
                            break;
                        case 'address1':
                            $record['prefectures_cd'] = $mGeneralPurposes->getPrefCdByPrefName($value);
                            if( $mGeneralPurposes->getPrefCdByPrefName($value))
                            {
                                $record[$excel_column[$pos]]=mb_substr($value,4,20);
                            }
                            else
                            {
                                $record[$excel_column[$pos]]=mb_substr($value,0,20);
                            }
                            break;
                        case 'phone_number':
                            if($value)
                            {
                                if($this->getCellularPhone($value))
                                {
                                    $record['cellular_phone_number']=$this->getCellularPhone($value);
                                }
                                else
                                {
                                    $record['landline_phone_number']=$value;
                                }
                            }
                            break;
                        case 'mst_business_office_id':
                            $record[$excel_column[$pos]] = $this->getOfficeId($value);
                            break;
                        case 'employment_pattern_id':
                            $data_kb = config('params.data_kb')['employment_pattern'];
                            $result = $this->checkExistDataAndInsert($data_kb, $value,config('params.import_file_path.mst_staffs.main_file_name'),$this->column_main_name['employment_pattern_id'], $row );
                            if ($result) {
                                $record[$excel_column[$pos]] = (string)$result;
                            }
                            break;
                        case 'sex_id':
                            $data_kb = config('params.data_kb')['sex'];
                            $result = $this->checkExistDataAndInsert($data_kb, $value,config('params.import_file_path.mst_staffs.main_file_name'),$this->column_main_name['sex_id'], $row );
                            if ($result) {
                                $record[$excel_column[$pos]] = (string)$result;
                            }
                            break;
                        default:
                            $record[$excel_column[$pos]] = (string)$value;
                            break;
                    }
                }
                $record["password"]=bcrypt($this->generateRandomString(8));
                unset($record['staff_nm']);
                unset($record['staff_nm_kana']);
                unset($record["phone_number"]);
            }
        }
        if(!empty($record))
        {
            $this->validateRow($record);
        }
    }

    protected function validateRow($record){
        if( !empty($this->ruleValid) ){
            $validator = Validator::make( $record, $this->ruleValid ,$this->messagesCustom ,$this->labels );
                if ($validator->fails()) {
                    $this->numErr++;
                    $failedRules = $validator->failed();
                    if (isset($failedRules['staff_cd']['Required'])) {
                        $this->log("DataConvert_Err_required",Lang::trans("log_import.required",[
                            "fileName" =>  config('params.import_file_path.mst_staffs.main_file_name'),
                            "fieldName" => $this->column_main_name['staff_cd'],
                            "row" => $this->rowIndex,
                        ]));
                    }
                    if (isset($failedRules['staff_cd']['Unique'])) {
                        $this->log("DataConvert_Err_ID_Match",Lang::trans("log_import.unique_staff_cd",[
                            "fileName" =>  config('params.import_file_path.mst_staffs.main_file_name'),
                            "fieldName" => $this->column_main_name['staff_cd'],
                            "row" => $this->rowIndex,
                        ]));
                    }
                    if (isset($failedRules['last_nm_kana']['Kana']) || isset($failedRules['first_nm_kana']['Kana'])) {
                        $this->log("DataConvert_Err_KANA",Lang::trans("log_import.check_kana",[
                            "fileName" =>  config('params.import_file_path.mst_staffs.main_file_name'),
                            "fieldName" => $this->column_main_name['staff_nm_kana'],
                            "row" => $this->rowIndex,
                        ]));
                    }
                    foreach ($failedRules as $field => $errors){
                            foreach ($errors as $ruleName => $error){
                                if($ruleName=='Length'){
                                    $this->log("data_convert",Lang::trans("log_import.check_length_and_trim",[
                                        "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                                        "excelFieldName" => $this->column_main_name[$field],
                                        "row" => $this->rowIndex,
                                        "excelValue" => $record[$field],
                                        "tableName" => $this->table,
                                        "DBFieldName" => $field,
                                        "DBvalue" => substr($record[$field],0,$error[0]),
                                    ]));
                                    $record[$field] = substr($record[$field],0,$error[0]);
                                }
                            }
                    }
                }
                else
                {
                    $this->exportPassword($record);
                    $this->insertDB($record);
                }

        }
    }
    protected function validAfter($validator,$data){}
    protected function exportPassword($record)
    {
        $password_random=$this->password_random;
        if (!empty($record)) {
            Excel::load($this->path, function($doc) use($password_random) {

                $sheet = $doc->setActiveSheetIndex(0);
                $sheet->setCellValue('AF', $password_random);

            })->export('xlsx');
        }
    }
    public function insertDB($record)
    {
        DB::beginTransaction();
        try{
            if (!empty($record)) {
                DB::table('mst_staffs')->insert($record);
                DB::commit();
            }
        }catch (\Exception $e){
            DB::rollback();
            $this->log("DataConvert_Err_SQL",Lang::trans("log_import.insert_error",[
                "fileName" => config('params.import_file_path.mst_staffs.main_file_name'),
                "row" => $this->rowIndex,
                "errorDetail" => $e->getMessage(),
            ]));
        }
    }

}
