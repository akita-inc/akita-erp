<?php
namespace App\Console\Commands\ImportExcel;
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
    public $type = "";
    public $configDataImport = [];
    public $numRead = 0;
    public $numNormal = 0;
    public $numErr = 0;
    public $dateTimeRun = "";

    public function __construct()
    {
        $this->dateTimeRun = date("y/m/d h:s:i");
    }

    protected function log($type,$message){
        $arrayLogPath = config("params.log_import_path");
        switch ($type){
            case "DataConvert_Err_ID_Match":
                $path = storage_path('logs/DataConvert_Err_SQL_社員扶養者_'.$this->dateTimeRun.".log");
                break;
            default:
                $path = $arrayLogPath[$type];
                break;
        }
        $contentLog = "";
        if(file_exists($arrayLogPath[$type])){
            $contentLog = file_get_contents( $path );
        }
        $contentLog .= date("y/m/d h:s:i ").$message."\n";
        file_put_contents($path,$contentLog);
    }

    protected function import(){

    }

    public function run(){
        if( !file_exists($this->path) ){
            echo "File don't exist";
        }else{
            if( !empty( Lang::trans("log_import.begin_start.mst_staff_dependents") ) ){
                $this->log("data_convert",Lang::trans("log_import.begin_start.mst_staff_dependents"));
            }
            try {
                $inputFileType = \PHPExcel_IOFactory::identify($this->path);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($this->path);
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
        }
    }
}
