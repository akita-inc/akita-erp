<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


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

    public $messagesCustom = [
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
    protected function saveBlock($id, $dataBlocks = array(), $tableName, $prefixField = null, $unsetFields = array())
    {
        if (count($dataBlocks) > 0) {
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
                if (!DB::table($tableName)->insert($arrayInsert)) {
                    DB::rollBack();
                    return false;
                }
            }
        }
        return true;
    }

    protected function saveStaffAuth($id, $data= array()){
        $mStaffAuth = new MStaffAuths();
        $dataInsert = [];
        foreach ($data as $key => $value){
            switch ($key){
                case 1:
                    foreach ($value['staffScreen'] as $id){
                        array_push($dataInsert, array('mst_screen_id' => $id,'accessible_kb' => $value['accessible_kb'],'mst_staff_id' => $id));
                    }
                    break;
                default:
                    $mst_screen = MScreens::where('screen_category_id',$value['screen_category_id'])->first();
                    array_push($dataInsert, array('mst_screen_id' => $mst_screen->id,'accessible_kb' => $value['accessible_kb'],'mst_staff_id' => $id));
            }
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
}