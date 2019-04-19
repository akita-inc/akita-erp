<?php
namespace App\Console\Commands\ImportExcel;
use App\Models\MStaffDependents;
use App\Models\MStaffs;

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
    public $fileName = "扶養者名_生年月日.xlsx";
    public $startRow = 3;

    public function __construct()
    {
        $this->path = config('params.import_file_path.mst_staff_dependents');
    }

    public function import(){
        $flagError = false;
        $mst_staff_dependentes = new MStaffDependents();
        $mst_staff_cd = (int)$this->rowCurrentData["A"];
        $mstStaff = null;
        if( empty($mst_staff_cd) ){
            $flagError = true;
            $this->log("DataConvert_Err_required",trans("log_import.required",[
                "fileName" => $this->fileName,
                "fieldName" => "社員CD",
                "row" => $this->rowIndex
            ]));
        }else{
            $mstStaff = MStaffs::where("staff_cd","=",$mst_staff_cd)->first();
            if(empty($mstStaff)){
                $flagError = true;
                $this->log("DataConvert_Err_required",trans("log_import.required",[
                    "fileName" => $this->fileName,
                    "fieldName" => "社員CD",
                    "row" => $this->rowIndex
                ]));
            }else{
                $mst_staff_dependentes->mst_staff_id = $mstStaff->id;
            }
            dd($mstStaff);
        }
        $mst_staff_id = "";
        dd($this->rowCurrentData);
    }
}
