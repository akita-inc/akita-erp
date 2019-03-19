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
    protected function saveBlock($id, $dataBlocks, $tableName,$flagEdit=null,$prefixField = null, $unsetFields = array())
    {
        $arrayIDInsert = [];
        $currentTime = date("Y-m-d H:i:s",time());
        $flagUpdate=null;
        if (count($dataBlocks) > 0) {
            $disp_number = 1;
            foreach ($dataBlocks as $key => $item) {
                $arrayField = [];
                $fields = array_keys($item);
                foreach ($fields as $fieldInput) {
                    $fieldDB = str_replace($prefixField, "", $fieldInput);
                    $arrayField += [$fieldDB => $item[$fieldInput]];
                }
                $arrayState = [
                    'mst_staff_id' => $id,
                    'disp_number' => $key + 1,
                ];
                $arrayInsert = $arrayField + $arrayState;
                if (count($unsetFields) > 0) {
                    foreach ($unsetFields as $field) {
                        unset($arrayInsert[$field]);
                    }
                }
                if(isset($item["id"]) && $item["id"] ){
                    $item["modified_at"] = $currentTime;
                    DB::table($tableName)->where("id", "=", $item["id"])->update($arrayInsert);
                    $arrayIDInsert[] = $flagUpdate = $item["id"];
                }
                else
                {
                    $ids=DB::table($tableName)->insertGetId($arrayInsert);
                    $arrayIDInsert[] = $ids;
                }
                if(!$flagUpdate){
                    DB::rollBack();
                    return false;
                }
                $disp_number++;

            }
        }
        if(isset($flagEdit)) {
            $deleteRows = DB::table($tableName)
                ->where("mst_staff_id", $id);
            if (!empty($arrayIDInsert)) {
                $deleteRows = $deleteRows->whereNotIn("id", $arrayIDInsert);
            }
            $deleteRows->delete();
        }
        return true;
    }

    protected function saveStaffAuth($id, $data= array()){
        $mStaffAuth = new MStaffAuths();
        $dataInsert = [];
        $allScreen = MScreens::all();
        foreach ($allScreen as $item){
            if($item->screen_category_id==1){
                $accessible_kb = in_array($item->id,$data[$item->screen_category_id]['staffScreen']) ? $data[$item->screen_category_id]['accessible_kb'] : 9;
            }
           else{
                $accessible_kb = $data[$item->screen_category_id]['accessible_kb'];
            }

            array_push($dataInsert, array(
                'mst_screen_id' => $item->id,
                'accessible_kb' => $accessible_kb,
                'mst_staff_id' => $id
            ));
        }
        if(count($dataInsert) > 0){
            DB::beginTransaction();
            try
            {
                $mStaffAuth->insert($dataInsert);
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
        //upload file
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
