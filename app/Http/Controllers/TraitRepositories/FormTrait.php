<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


use App\Models\MModifyLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

trait FormTrait
{
    protected function save( $data ){
        return DB::table($this->table)->insertGetId( $data );
    }

    protected function validAfter( &$validator,$data ){}

    protected function beforeSubmit($data){}

    public function submit(Request $request)
    {
        $data = $request->all();
        if($request->route()->getPrefix()=='staffs/api-v1'){
            $image = $data['image'];
            $data = json_decode($data['data'], true);
            $data['drivers_license_picture'] = $image;
            array_walk_recursive($data, function (& $item, $key) {if ($item=='') { $item = null; }});
        }
        $this->beforeSubmit($data);
        $idInsert = "";
        if( !empty($this->ruleValid) ){
            $validator = Validator::make( $data, $this->ruleValid ,$this->messagesCustom ,$this->labels );

            $validator->after(function($validator) use ($data) {
                $this->validAfter($validator,$data);
            });
            if ( $validator->fails() ) {
                return response()->json([
                    'success'=>FALSE,
                    'message'=> $validator->errors()
                ]);
            }else{
                $dataBeforeUpdate = [];
                if( isset($data["id"]) && (!isset($this->add_log) || $this->add_log == true) ){
                    $dataBeforeUpdate = DB::table($this->table)->where("id","=",$data["id"])->first();
                }
                if( !( $idInsert = $this->save( $data ) ) ){
                    return response()->json([
                        'success'=>FALSE,
                        'message'=> ["SaveFail"=>trans('common.save_fail')]
                    ]);
                }
                if( isset($data["id"]) && (!isset($this->add_log) || $this->add_log == true)) {
                    $this->addLogModify($dataBeforeUpdate,$data);
                }
            }
        }
        return response()->json([
            'success'=>true,
            'message'=> [],
            'idInsert' => $idInsert
        ]);
    }

    protected function addLogModify( $dataBeforeUpdate,$data ){
        if(isset($data["id"])){
            $modifyLog = new MModifyLogs();
            $modifyLog->writeLogWithTable( $this->table,$dataBeforeUpdate,$data,$data["id"] );
        }
    }

    public function backHistory(){
        Session::put('backQueryFlag', true);
    }


    public function checkIsExist(Request $request, $id){
        $status= $request->get('status');
        $mode = $request->get('mode');
        $modified_at = $request->get('modified_at');
        $data = DB::table($this->table)->where('id',$id)->whereNull('deleted_at')->first();
        if (isset($data)) {
            if($this->table!='empty_info' || ($mode!='edit' && $this->table=='empty_info') || ($mode=='edit' && $this->table=='empty_info' && Session::get('sysadmin_flg')==1)){
                if(!is_null($modified_at)){
                    if(Carbon::parse($modified_at) != Carbon::parse($data->modified_at)){
                        $message = Lang::get('messages.MSG04003');
                        return Response()->json(array('success'=>false, 'msg'=> $message));
                    }
                }
            }
            return Response()->json(array('success'=>true));
        } else {
            if($this->table=='empty_info'){
                if($mode=='edit' || $mode=='delete'){
                    $message = Lang::get('messages.MSG04004');
                }else{
                    switch ($status){
                        case 1:
                            $message = Lang::get('messages.MSG10021');
                            break;
                        case 2:
                            $message = Lang::get('messages.MSG10015');
                            break;
                        case 8:
                            $message = Lang::get('messages.MSG10018');
                            break;
                    }
                }
                return Response()->json(array('success'=>false, 'msg'=> $message));
            }else{
                return Response()->json(array('success'=>false, 'msg'=> is_null($mode) ? Lang::trans('messages.MSG04003') : Lang::trans('messages.MSG04004')));
            }
        }
    }
}
