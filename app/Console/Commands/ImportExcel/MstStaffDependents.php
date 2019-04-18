<?php
namespace App\Console\Commands\ImportExcel;
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/17/2019
 * Time: 3:06 PM
 */
class MstStaffDependents extends BaseImport
{
    public $path = "";
    public $configDataImport = [];
    public $startRow = 3;

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staff_dependents');
    }

    public function import(){
        $flagError = false;
        $mst_staff_cd = (int)$this->rowCurrentData["A"];
        if( empty($mst_staff_cd) ){
            $flagError = true;
            $this->log("DataConvert_Err_required",trans("log_import.required",[
                "nameExcel",
                "fieldExcel",
                "rowIndexExcel"
            ]));
        }
        $mst_staff_id = "";
        dd($this->rowCurrentData);
    }
}
