<?php
namespace App\Console\Commands\ImportExcel;
use App\Models\MStaffDependents;
use App\Models\MStaffs;
use Illuminate\Support\Facades\Lang;

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
        parent::__construct();
    }

    public function import(){
        $flagError = false;
        $this->rowCurrentData = $this->rowCurrentData[$this->rowIndex];
        $dataInsert = [];
        $mst_staff_cd = $this->rowCurrentData["A"];
        $mstStaff = null;
        if( empty($mst_staff_cd) && (string)$mst_staff_cd!= '0'){
            $this->log("DataConvert_Err_required",trans("log_import.required",[
                "fileName" => $this->fileName,
                "fieldName" => "社員CD",
                "row" => $this->rowIndex
            ]));
            $this->numErr++;
        }
        else{
            $mstStaff = MStaffs::where("staff_cd","=",(string)$mst_staff_cd)->first();
            if(empty($mstStaff)){
                $flagError = true;
                $this->log("DataConvert_Err_ID_Match",trans("log_import.no_record_in_extra_file",[
                    "mainFileName" => $this->fileName,
                    "fieldName" => "社員CD",
                    "row" => $this->rowIndex,
                    "extraFileName" => "mst_staffs",
                ]));
            }
            else{
                $dataInsert["mst_staff_id"] = $mstStaff->id;
                $arrayInsert = [];
                $strLenFirstName = 25;
                if(!empty($this->rowCurrentData["H"])){
                    $strSlit = explode(",",str_replace("、",",",$this->rowCurrentData["H"]));
                    $strBegin = count($arrayInsert);
                    foreach ($strSlit as $value){
                        $arrayInsert[]  = [];
                        $strCheck = "配偶者";
                        $firstName = $value;
                        $firstName = str_replace("（","(",$firstName);
                        $firstName = str_replace("）",")",$firstName);
                        $beginSub = strpos($firstName,"(");
                        $endSub = strpos($firstName,")");
                        if($beginSub && $endSub){
                            $strReplace = substr($firstName,$beginSub,($endSub - $beginSub) + 1);
                            $firstName = str_replace($strReplace, "", $firstName);
                            $strCheck = "扶養者";
                        }

                        if(mb_strlen($firstName) > $strLenFirstName){
                            $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                                "fileName" => $this->fileName,
                                "excelFieldName" => "配偶者",
                                "row" => $this->rowIndex,
                                "excelValue" => $firstName,
                                "tableName" => $this->table,
                                "DBFieldName" => "first_nm",
                                "DBvalue" => 'null',
                            ]));
                            $firstName = null;
                        }

                        $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                        $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                            $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"),$strCheck,
                                $this->fileName,"配偶者",$this->rowIndex);
                    }
                    $strSlitBD = explode(",",str_replace("、",",",$this->rowCurrentData["B"]));
                    foreach ($strSlitBD as $key=>$value){
                        if(isset($arrayInsert[$strBegin + $key])){
                            $arrayInsert[$strBegin + $key]["birthday"] = $value;
                        }
                    }

                }
                $arrayColumn = [
                        ["firstName" => "I", "birthday" => "C", "fieldExcel" => "扶養者氏名１"],
                        ["firstName" => "J", "birthday" => "D", "fieldExcel" => "扶養者氏名２"],
                        ["firstName" => "K", "birthday" => "E", "fieldExcel" => "扶養者氏名３"],
                        ["firstName" => "L", "birthday" => "F", "fieldExcel" => "扶養者氏名４"],
                        ["firstName" => "M", "birthday" => "G", "fieldExcel" => "扶養者氏名５"],
                    ];
                foreach ($arrayColumn as $key => $column){
                    if(!empty($this->rowCurrentData[$column["firstName"]])){
                        $strSlit = explode(",",str_replace("、",",",$this->rowCurrentData[$column["firstName"]]));
                        $strBegin = count($arrayInsert);
                        foreach ($strSlit as $value) {
                            $arrayInsert[] = [];
                            $strCheck = "扶養者";
                            $firstName = $value;
                            $firstName = str_replace("（","(",$firstName);
                            $firstName = str_replace("）",")",$firstName);
                            $beginSub = strpos($firstName,"(");
                            $endSub = strpos($firstName,")");
                            if($beginSub && $endSub){
                                $strReplace = substr($firstName,$beginSub,($endSub - $beginSub) + 1);
                                $firstName = str_replace($strReplace, "", $firstName);
                                $strCheck = "扶養者";
                            }

                            if(mb_strlen($firstName) > $strLenFirstName){
                                $this->log("DataConvert_Trim", Lang::trans("log_import.check_length_and_trim", [
                                    "fileName" => $this->fileName,
                                    "excelFieldName" => $column["fieldExcel"],
                                    "row" => $this->rowIndex,
                                    "excelValue" => $firstName,
                                    "tableName" => $this->table,
                                    "DBFieldName" => "first_nm",
                                    "DBvalue" => 'null',
                                ]));
                                $firstName = null;
                            }

                            $arrayInsert[count($arrayInsert) - 1]["first_nm"] = $firstName;
                            $arrayInsert[count($arrayInsert) - 1]["dependent_kb"] =
                                $this->checkExistDataAndInsert(config("params.data_kb.dependent_kb"), $strCheck,
                                    $this->fileName, "扶養者氏名１", $this->rowIndex);
                        }
                        $strSlitBD = explode(",",str_replace("、",",",$this->rowCurrentData[$column["birthday"]]));
                        foreach ($strSlitBD as $key=>$value){
                            if(isset($arrayInsert[$strBegin + $key])){
                                $arrayInsert[$strBegin + $key]["birthday"] = $value;
                            }
                        }
                    }
                }
                $dataInsert["rows"] = $arrayInsert;
            }
            if( !$flagError ){
                if(count($dataInsert["rows"]) > 0 ) {
                    foreach ($dataInsert["rows"] as $item) {
                        $mstStaffDependents = MStaffDependents::where("mst_staff_id", "=", $dataInsert["mst_staff_id"])
                            ->where("first_nm", "=", $item["first_nm"])
                            ->first();
                        if (empty($mstStaffDependents)) {
                            $mstStaffDependents = new MStaffDependents();
                            $mstStaffDependents->mst_staff_id = $dataInsert["mst_staff_id"];
                            $mstStaffDependents->last_nm = $mstStaff["last_nm"];
                            if (!empty($item["birthday"])) {
                                $mstStaffDependents->birthday = \PHPExcel_Style_NumberFormat::toFormattedString($item["birthday"], 'YYYY-MM-DD');
                            }
                            $mstStaffDependents->dependent_kb = $item["dependent_kb"];
                            $mstStaffDependents->first_nm = $item["first_nm"];
                        } else {
                            if (!empty($item["birthday"])) {
                                $mstStaffDependents->birthday = \PHPExcel_Style_NumberFormat::toFormattedString($item["birthday"], 'YYYY-MM-DD');
                            } else {
                                $mstStaffDependents->birthday = null;
                            }
                        }


                        try {
                            $mstStaffDependents->save();
                            $this->numNormal++;
                        } catch (\Exception $e) {
                            $this->log("DataConvert_Err_SQL", Lang::trans("log_import.insert_error", [
                                "fileName" => $this->fileName,
                                "row" => $this->rowIndex,
                                "errorDetail" => $e->getMessage(),
                            ]));
                            $this->numErr++;
                        }
                    }
                }
            }
            else{
                $this->numErr++;
            }
        }
    }
}
