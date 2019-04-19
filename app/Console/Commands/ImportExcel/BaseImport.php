<?php
namespace App\Console\Commands\ImportExcel;
use App\Models\MGeneralPurposes;
use Illuminate\Support\Facades\Lang;

/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:07 PM
 */
class BaseImport{
    public $path = "";
    public $rowCurrentData = "";
    public $rowIndex = "";
    public $table = "";
    public $configDataImport = [];
    public $numRead = 0;
    public $numNormal = 0;
    public $numErr = 0;
    public $dateTimeRun = "";
    public $startRow = 2;
    public $objPHPExcel="";
    public $tableLabel = [
        'mst_staffs' => '社員',
        'mst_staff_dependents' => '社員扶養者',
        'mst_staff_qualifications' => '社員保有資格',
        'mst_vehicles' => '車両',
        'mst_customers' => '得意先',
        'mst_suppliers' => '仕入先',
        'mst_business_offices' => '営業所',
        'mst_bill_issue_destinations' => '請求書発行先住所',
    ];

    public function __construct()
    {
        $this->dateTimeRun = date("YmdHis");
    }

    protected function log($type,$message){
        $arrayLogPath = config("params.log_import_path");
        switch ($type){
            case "DataConvert_Err_ID_Match":
                $path = storage_path('logs/DataConvert_Err_ID_Match_'.$this->tableLabel[$this->table].'_'.$this->dateTimeRun.".log");
                break;
            case "DataConvert_Add_general_purposes":
                $path = storage_path('logs/DataConvert_Add_general_purposes_'.$this->dateTimeRun.".log");
                break;
            case "DataConvert_Err_SQL":
                $path = storage_path('logs/DataConvert_Err_SQL_'.$this->tableLabel[$this->table].'_'.$this->dateTimeRun.".log");
                break;
            case "DataConvert_Err_required":
                $path = storage_path('logs/DataConvert_Err_required_'.$this->tableLabel[$this->table].'_'.$this->dateTimeRun.".log");
                break;
            case "DataConvert_Err_KANA":
                $path = storage_path('logs/DataConvert_Err_KANA_'.$this->tableLabel[$this->table].'_'.$this->dateTimeRun.".log");
                break;
            case "DataConvert_Trim":
                $path = storage_path('logs/DataConvert_Trim_'.$this->tableLabel[$this->table].'_'.$this->dateTimeRun.".log");
                break;
            default:
                $path = $arrayLogPath[$type];
                break;
        }

        $contentLog = "";
        if(file_exists($path)){
            $contentLog = file_get_contents( $path );
        }
        date_default_timezone_set("Asia/Tokyo");
        $contentLog .= date("Y/m/d H:i:s ").$message."\n";
        file_put_contents($path,$contentLog);
    }

    public function checkExistDataAndInsert($data_kb,$string,$fileName,$fieldName, $row){
        $mGeneralPurposes = new MGeneralPurposes();
        $query = $mGeneralPurposes->where('data_kb',$data_kb)
            ->where('deleted_at','=',null);
        if(is_numeric($string)){
            $result = $query->where('date_id',$string)->first();
        }else{
            $result = $query->where('date_nm','=',$string)->first();
        }
        if(!$result){
            if(is_numeric($string)){
                $this->log("DataConvert_Add_general_purposes",Lang::trans("log_import.add_general_purposes_number",[
                    "fileName" => $fileName,
                    "fieldName" => $fieldName,
                    "row" => $row,
                ]));
                return null;
            }
            $data = $mGeneralPurposes->where('data_kb',$data_kb)
                ->where('deleted_at','=',null)
                ->orderBy('disp_number','desc')
                ->get();
            $mGeneralPurposes->data_kb = $data_kb;
            $mGeneralPurposes->date_id = $data[0]->date_id+1;
            $mGeneralPurposes->data_kb_nm = $data[0]->data_kb_nm;
            $mGeneralPurposes->date_nm_kana = 'フメイ';
            $mGeneralPurposes->date_nm = $string;
            $mGeneralPurposes->disp_fg = 1;
            $mGeneralPurposes->disp_number = $data[0]->disp_number+1;
//            $mGeneralPurposes->save();
            $this->log("DataConvert_Add_general_purposes",Lang::trans("log_import.add_general_purposes_string",[
                "fileName" => $fileName,
                "fieldName" => $fieldName,
                "row" => $row,
                "data_kb" => $mGeneralPurposes->data_kb,
                "data_kb_nm" => $mGeneralPurposes->data_kb_nm,
                "date_id" => $mGeneralPurposes->date_id,
                "date_nm" => $mGeneralPurposes->date_nm,
            ]));
            return $mGeneralPurposes->date_id;
        }else{
            return $result->date_id;
        }

    }

    protected function import(){

    }

    public function run(){
        if( !file_exists($this->path) ){
            echo "File don't exist";
        }else{
            if( !empty( Lang::trans("log_import.begin_start.mst_staff_dependents") ) ){
                $this->log("data_convert",Lang::trans("log_import.begin_start",[
                    "table" => $this->tableLabel[$this->table],
                ]));
            }
            try {
                $inputFileType = \PHPExcel_IOFactory::identify($this->path);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($this->path);
                $this->objPHPExcel=$objPHPExcel;
            } catch(Exception $e) {
                return ('Error loading file "'.pathinfo($this->path,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $start_row = 2;
            for ($row = $start_row; $row <= $highestRow; $row++) {
                $this->rowIndex = $row;
                $this->rowCurrentData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, false, false, true);
                $this->import();
                $this->numRead++;
            }
            if( !empty( Lang::trans("log_import.end_read") ) ){
                $this->log("data_convert",Lang::trans("log_import.end_read",[
                    "numRead" => $this->numRead,
                    "numNormal"=> $this->numNormal,
                    "numErr" => $this->numErr
                ]));
            }
            if($this->table=="mst_staffs")
            {
                $this->exportPassword();
            }
        }
    }
    protected function exportPassword()
    {
        $objPHPExcel=$this->objPHPExcel;
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, 1,'ログインパスワード');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $file=storage_path('import/dbo_M_社員'.$this->dateTimeRun.'.xlsx');
        $objWriter->save($file);
    }
}
