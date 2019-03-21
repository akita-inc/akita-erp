<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


use App\Helpers\Common;
use App\Models\MScreens;
use App\Models\MStaffAuths;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\TraitRepositories\FormTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
trait StaffTrait
{
    public $allNullAble = false;
    public $labels = [
    ];

    protected function validateBlockCollapse(&$validator,$name,$data=array(),$rules=array())
    {
        $dataBlocks = $data[$name];
        $nameBlocks=$name;
        $errorsEx = [];
        if (count($dataBlocks) > 0) {
            foreach ($dataBlocks as $index => $items) {
                $validatorEx = Validator::make($items, $rules, $this->messagesCustom, $this->labels);
                $validatorEx->after(function ($validatorEx) use ($items,$nameBlocks)  {
                    $this->addAdditionRules($validatorEx,$items,$nameBlocks);
                });
                if ($validatorEx->fails()) {
                    $errorsEx[$index] = $validatorEx->errors();
                }
            }
        }
        if( count($errorsEx) > 0){
            $validator->errors()->add($nameBlocks,$errorsEx);
        }
        return true;
    }

    protected function addAdditionRules( &$validator,$data,$nameBlocks ){
        switch ($nameBlocks)
        {
            case "mst_staff_job_experiences":
                if (Carbon::parse($data['staff_tenure_start_dt']) > Carbon::parse($data['staff_tenure_end_dt'])) {
                    $validator->errors()->add('staff_tenure_start_dt', str_replace(' :attribute', $this->labels['staff_tenure_start_dt'], Lang::get('messages.MSG02014')));
                }
                break;
            case "mst_staff_qualifications":
                if (Carbon::parse($data['period_validity_start_dt']) > Carbon::parse($data['period_validity_end_dt'])) {
                    $validator->errors()->add('period_validity_start_dt', str_replace(' :attribute', $this->labels['period_validity_start_dt'], Lang::get('messages.MSG02014')));
                }
                break;
            case "mst_staff_dependents":
                break;
        }

    }
    protected function saveAccordion($id, $data, $name, $prefixField = null, $unsetFields = array(), $currentTime)
    {
        $dataAccordions=$data[$name];
        $arrayIDInsert=[];
        $this->allNullAble = true;
        if (count($dataAccordions) > 0) {
            foreach ($dataAccordions as $key => $item) {
                foreach ($item as $valueChk){
                    if(!empty($valueChk)){
                        $this->allNullAble = false;
                    }
                }
                $arrayField = [];
                $fields = array_keys($item);
                foreach ($fields as $fieldInput) {
                    $fieldDB = str_replace($prefixField, "", $fieldInput);
                    $arrayField += [$fieldDB => $item[$fieldInput]];
                }
                $arrayState = [
                    'mst_staff_id' => $id,
                    'disp_number' => $key + 1,
                    "modified_at" => $currentTime,
                    "created_at"=>$currentTime
                ];
                $arrayInsert = $arrayField + $arrayState;
                if (count($unsetFields) > 0) {
                    foreach ($unsetFields as $field) {
                        unset($arrayInsert[$field]);
                    }
                }
                if(!$this->allNullAble)
                {
                    if(isset($item["id"]) && $item["id"] && !isset($data["clone"]))
                    {
                        unset($arrayInsert['created_at']);
                        $idAccordionUpdate=$this->updateRowsAccordion($arrayInsert,$name);
                        array_push($arrayIDInsert,$idAccordionUpdate);
                    }
                    else
                    {
                        $idAccordionInsert=$this->insertRowsAccordion($arrayInsert,$name);
                        array_push($arrayIDInsert,$idAccordionInsert);
                    }
                }
            }
        }
        $this->deleteRowsAccordion($data,$arrayIDInsert,$name,$currentTime);
        return true;
    }
    protected function updateRowsAccordion($data,$name)
    {
        try{
            DB::table($name)->where("id","=",$data["id"])->update($data);
            $idUpdate = $data["id"];
        }
        catch (\Exception $e)
        {
            DB::rollback();
            dd($e);
            return false;
        }
        return $idUpdate;
    }
    protected function insertRowsAccordion($data,$name)
    {
        try {
            $idInsert = DB::table($name)->insertGetId($data);
            $arrayIDInsert[] = $idInsert;
        }
        catch(\Exception $e)
        {
            DB::rollback();
            dd($e);
            return false;
        }
        return $idInsert;
    }
    protected function deleteRowsAccordion($data,$arrayIDInsert,$name,$currentTime)
    {

        try {
            if (isset($data["id"]) && $data["id"] && !isset($data["clone"])) {
                DB::table($name)
                    ->where("mst_staff_id", $data["id"])
                    ->whereNotIn("id", $arrayIDInsert)
                    ->update(['deleted_at' => $currentTime]);
                return true;
            }
        }
        catch(\Exception $e)
        {
            DB::rollback();
            return false;
        }
    }
    protected function saveStaffAuth($id, $data= array(), $currentTime){
        $mStaffAuth = new MStaffAuths();
        $dataInsert = [];
        $allScreen = MScreens::all();
        foreach ($data as $key => $value){
            switch ($key){
                case "1":
                    foreach ($value['staffScreen'] as $screen_id){
                        array_push($dataInsert, array('mst_screen_id' => (int)$screen_id,'accessible_kb' => $value['accessible_kb']
                        ,'mst_staff_id' => $id, 'created_at' => $currentTime, 'modified_at' => $currentTime));
                    }
                    break;
                default:
                    $mst_screen = MScreens::where('screen_category_id',$value['screen_category_id'])->first();
                    array_push($dataInsert, array('mst_screen_id' => $mst_screen->id,'accessible_kb' => $value['accessible_kb']
                    ,'mst_staff_id' => $id, 'created_at' => $currentTime, 'modified_at' => $currentTime));
            }
        }
        if(count($dataInsert) > 0){
            DB::beginTransaction();
            try
            {
                if($mStaffAuth->where('mst_staff_id','=', $id)->count() > 0){
                    foreach ($allScreen as $item){
                        $key = array_search($item->id, array_column($dataInsert, 'mst_screen_id'));
                        $dataExist = $mStaffAuth->where('mst_staff_id','=',$id)->where('mst_screen_id' ,'=',$item->id)->first();
                        if(is_numeric($key)){
                            if(!is_null($dataExist)){
                                unset($dataInsert[$key]["created_at"]);
                                $mStaffAuth->where('id',$dataExist->id)->update($dataInsert[$key]);
                            }else{
                                $mStaffAuth->insert($dataInsert[$key]);
                            }
                        }else{
                            if(!is_null($dataExist)) {
                                $mStaffAuth->where('id', $dataExist->id)->delete();
                            }
                        }
                    }
                }else{
                    $mStaffAuth->insert($dataInsert);
                }
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
                dd($e);
                return false;
            }
        }
        return true;
    }
    protected function uploadFile($id,$file,$path)
    {
        $directoryPath = $path. $id;
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        if(!is_null($file)) {
            try {
                $fileName = Common::uploadFile($file, $directoryPath);
                DB::table('mst_staffs')
                    ->where('id', $id)
                    ->update(['drivers_license_picture' => $fileName]);
            } catch (\Exception $e) {
                dd($e);
                return false;
            }
        }
        return true;
    }

    protected function deleteFile($id,$deleteFile)
    {
        if (isset($deleteFile) && $deleteFile!='') {
            try
            {
                DB::table('mst_staffs')
                    ->where('id', $id)
                    ->update(['drivers_license_picture' => null]);
            }catch (\Exception $e) {
                dd($e);
                return false;
            }
            return true;
        }
        return true;
    }
}
