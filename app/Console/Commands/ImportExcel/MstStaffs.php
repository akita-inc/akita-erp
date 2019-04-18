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
class MstStaffs extends BaseImport
{
    public $path = "";
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
    public $excel_column_insurer=[
        'staff_cd'=>'社員番号',
        'insurer_number'=>'保険番号',
        'basic_pension_number'=>'基礎年金番号',
        'person_insured_number'=>'被保険者番号',
        'health_insurance_class'=>'健康保険等級',
        'welfare_annuity_class'=>'厚生年金等級',
        'relocation_municipal_office_cd'=>'市町村役場コード',
    ];
    public $excel_column_edu_bg=[
        'staff_cd'=>'社員CD',
        'educational_background'=>'最終学歴',
        'educational_background_dt'=>'最終学歴日付',
        'retire_reasons'=>'退職理由',
        'death_reasons'=>'死亡理由',
        'death_dt'=>'死亡年月日'

    ];
    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staffs.main');
    }

    public function import()
    {
        echo "mst_staffs";
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
    public  function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function mainReading($rowData,$row){
        $excel_column = $this->excel_column;
        $record = array();
        $mGeneralPurposes = new MGeneralPurposes();
        $insuranceArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.health_insurance_card_information'),'insurance');
        $backgroundArr=$this->getDataFromChildFile(config('params.import_file_path.mst_staffs.staff_background'),'staff_background');
        foreach($rowData[$row] as $pos=>$value){
            if(isset($excel_column[$pos])) {
                switch ($excel_column[$pos]){
                    case 'created_at':
                    case 'modified_at':
                        $record[$excel_column[$pos]] = $this->formatDateTimeString($value);
                        break;
                    case 'birthday'||'enter_date'|| 'retire_date':
                        $record[$excel_column[$pos]] = $this->formatDateString($value);
                        break;
                    case 'staff_cd':
                        $record[$excel_column[$pos]] = substr($value,0,5);
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
                        $record[$excel_column[$pos]] = $mGeneralPurposes->getPrefCdByPrefName($value);
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
                    default:
                        $record[$excel_column[$pos]] = $value;
                        break;
                }
            }
            $record["password"]=bcrypt($this->generateRandomString(8));
            unset($record['staff_nm']);
            unset($record['staff_nm_kana']);
            unset($record["phone_number"]);
        }
        dd($record);
        if(!empty($record))
        {
            $this->insertDB($record);
        }
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
    public function explodeStaffName($value,$type)
    {
        $result=array();
        if($type=="kana")
        {
                $staff_nm=explode(' ',$value);
                $result['last_nm_kana']=isset($staff_nm[0])?$staff_nm[0]:null;
                $result['first_nm_kana']=isset($staff_nm[1])?$staff_nm[1]:null;
        }
        else
        {
                $staff_nm=explode('　',$value);
                $result['last_nm']=isset($staff_nm[0])?$staff_nm[0]:null;
                $result['first_nm']=isset($staff_nm[1])?$staff_nm[1]:null;
        }
        return $result;
    }
    public  function getDataFromChildFile($path,$type)
    {
        try {
            $excel_column_insurer=$this->excel_column_insurer;
            $excel_column_edu_bg=$this->excel_column_edu_bg;
            $data = Excel::load($path)->get();
            if($data->count()){
                if($type=="insurance")
                {
                    foreach ($data as  $value) {
                        $arr[$value->{$excel_column_insurer['staff_cd']}] = [
                            'insurer_number'=>substr($value->{$excel_column_insurer['insurer_number']},0,3),
                            'basic_pension_number'=>substr($value->{$excel_column_insurer['basic_pension_number']},0,11),
                            'person_insured_number'=>substr($value->{$excel_column_insurer['person_insured_number']},0,11),
                            'health_insurance_class'=>$value->{$excel_column_insurer['health_insurance_class']},
                            'welfare_annuity_class'=>$value->{$excel_column_insurer['welfare_annuity_class']},
                            'relocation_municipal_office_cd'=>$value->{$excel_column_insurer['relocation_municipal_office_cd']},
                        ];
                    }
                }
                elseif($type=="staff_background")
                {
                    foreach ($data as  $value) {
                        $arr[$value->{$excel_column_edu_bg['staff_cd']}] = [
                            'educational_background'=>$value->{$excel_column_edu_bg['educational_background']},
                            'educational_background_dt'=> $this->formatDateString($value->{$excel_column_edu_bg['educational_background_dt']}),
                            'retire_reasons'=>$value->{$excel_column_edu_bg['retire_reasons']},
                            'death_reasons'=>$value->{$excel_column_edu_bg['death_reasons']},
                            'death_dt'=>$this->formatDateString($value->{$excel_column_edu_bg['death_dt']}),
                        ];
                    }
                }
            }
            return $arr;
        } catch(\Exception $e) {
            return ('Error loading file "'.pathinfo($path,PATHINFO_BASENAME).'": '.$e->getMessage());
        }
    }
    public  function insertDB($data)
    {
        DB::beginTransaction();
        try
        {
            $idInsert=DB::table('mst_staffs')->insert($data);
            if($idInsert)
            {
                dd("insert success");
            }

        }
        catch (\Exception $e)
        {
            dd($e);
            DB::rollBack();
        }
        DB::commit();
    }
}
