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
        $this->rowCurrentData = $this->rowCurrentData[$this->rowIndex];
        $dataInsert = [];
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
                $dataInsert["mst_staff_id"] = $mstStaff->id;
                $arrayInsert = [];
                if(!empty($this->rowCurrentData["H"])){
                    $strSlit = explode(",",$this->rowCurrentData["H"]);
                    foreach ($strSlit as $value){
                        $arrayInsert[]  = [];
                        $strCheck = "配偶者";
                        $firstName = $value;
                        if(strpos($value,"(年少)")){
                            $strCheck = "配偶者";
                            $firstName = str_replace("(年少)","",$firstName);
                        }
                        if(strpos($value,"(母)")){
                            $strCheck = "配偶者";
                            $firstName = str_replace("(母)","",$firstName);
                        }
                        $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                        $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                            $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                                $this->fileName,"扶養者",$this->rowIndex);
                    }
                    $strSlitBD = explode(",",$this->rowCurrentData["B"]);
                    foreach ($strSlitBD as $key=>$value){
                        if(isset($arrayInsert[$key])){
                            $arrayInsert[count($arrayInsert) - 1]["birthday"] = $value;
                        }
                    }

                }
                if(!empty($this->rowCurrentData["I"])){
                    $arrayInsert[]  = [];
                    $strCheck = "配偶者";
                    $firstName = $this->rowCurrentData["I"];
                    if(strpos($this->rowCurrentData["I"],"(年少)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(年少)","",$firstName);
                    }
                    if(strpos($this->rowCurrentData["I"],"(母)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(母)","",$firstName);
                    }
                    $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                    $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                        $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                        $this->fileName,"扶養者",$this->rowIndex);
                    $arrayInsert[count($arrayInsert) - 1]["birthday"] = $this->rowCurrentData["C"];
                }
                if(!empty($this->rowCurrentData["J"])){
                    $arrayInsert[]  = [];
                    $strCheck = "配偶者";
                    $firstName = $this->rowCurrentData["J"];
                    if(strpos($this->rowCurrentData["J"],"(年少)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(年少)","",$firstName);
                    }
                    if(strpos($this->rowCurrentData["J"],"(母)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(母)","",$firstName);
                    }
                    $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                    $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                        $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                            $this->fileName,"扶養者",$this->rowIndex);
                    $arrayInsert[count($arrayInsert) - 1]["birthday"] = $this->rowCurrentData["D"];
                }
                if(!empty($this->rowCurrentData["K"])){
                    $arrayInsert[]  = [];
                    $strCheck = "配偶者";
                    $firstName = $this->rowCurrentData["K"];
                    if(strpos($this->rowCurrentData["K"],"(年少)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(年少)","",$firstName);
                    }
                    if(strpos($this->rowCurrentData["K"],"(母)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(母)","",$firstName);
                    }
                    $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                    $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                        $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                            $this->fileName,"扶養者",$this->rowIndex);
                    $arrayInsert[count($arrayInsert) - 1]["birthday"] = $this->rowCurrentData["E"];
                }
                if(!empty($this->rowCurrentData["L"])){
                    $arrayInsert[]  = [];
                    $strCheck = "配偶者";
                    $firstName = $this->rowCurrentData["L"];
                    if(strpos($this->rowCurrentData["L"],"(年少)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(年少)","",$firstName);
                    }
                    if(strpos($this->rowCurrentData["L"],"(母)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(母)","",$firstName);
                    }
                    $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                    $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                        $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                            $this->fileName,"扶養者",$this->rowIndex);
                    $arrayInsert[count($arrayInsert) - 1]["birthday"] = $this->rowCurrentData["F"];
                }
                if(!empty($this->rowCurrentData["M"])){
                    $arrayInsert[]  = [];
                    $strCheck = "配偶者";
                    $firstName = $this->rowCurrentData["M"];
                    if(strpos($this->rowCurrentData["M"],"(年少)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(年少)","",$firstName);
                    }
                    if(strpos($this->rowCurrentData["M"],"(母)")){
                        $strCheck = "配偶者";
                        $firstName = str_replace("(母)","",$firstName);
                    }
                    $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                    $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                        $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                            $this->fileName,"扶養者",$this->rowIndex);
                    $arrayInsert[count($arrayInsert) - 1]["birthday"] = $this->rowCurrentData["G"];
                }
                $dataInsert["rows"] = $arrayInsert;
            }
            if( !$flagError && count($dataInsert["rows"]) > 0 ){
                foreach ( $dataInsert["rows"] as $item ){
                    $mstStaffDependents = new MStaffDependents();
                    $mstStaffDependents->mst_staff_id = $dataInsert["mst_staff_id"];
                    $mstStaffDependents->last_nm = $mstStaff["last_nm"];
                    if(!empty($item["birthday"])){
                        $mstStaffDependents->birthday = \PHPExcel_Style_NumberFormat::toFormattedString($item["birthday"], 'YYYY-MM-DD');
                    }
                    $mstStaffDependents->dependent_kb = $item["dependent_kb"];
                    $mstStaffDependents->first_nm = $item["first_nm"];
                    try{
                        $mstStaffDependents->save();
                    }catch (\Exception $e){
                        $this->log("DataConvert_Err_SQL",$e->getMessage());
                    }
                }
            }
        }
    }
}
