<?php
namespace App\Console\Commands\ImportExcel;
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

    protected function log($type,$message){
        $arrayLogPath = [
            "DataConvert" => config("log_import_path")
        ];
        $path = $arrayLogPath[$type];
        if(file_exists($arrayLogPath[$type])){
            $contentLog = file_get_contents();
            $contentLog .= "\n".date("y/m/d h:s:i ").$message;
            file_put_contents($path,$contentLog);
        }
    }

    protected function import(){

    }

    public function run(){
        if( !file_exists($this->path) ){
            echo "File don't exist";
        }else{
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
            }
        }
    }
}
