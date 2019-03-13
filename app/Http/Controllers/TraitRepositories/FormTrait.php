<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

trait FormTrait
{
    protected function save( $data ){
        return $this->query->insertGetId( $data );
    }

    public function submit(Request $request)
    {
        $data = $request->all();
        $idInsert = "";
        if( !empty($this->ruleValid) ){
            $validator = Validator::make( $data, $this->ruleValid,$this->messagesCustom );
            if ( $validator->fails() ) {
                return response()->json([
                    'success'=>FALSE,
                    'message'=> $validator->errors()
                ]);
            }else{
                if( !( $idInsert = $this->save( $data ) ) ){
                    return response()->json([
                        'success'=>FALSE,
                        'message'=> ["SaveFail"=>trans('common.save_fail')]
                    ]);
                }
            }
        }
        return response()->json([
            'success'=>true,
            'message'=> [],
            'idInsert' => $idInsert
        ]);
    }

    public function backHistory(Request $request){
        Session::put('backQueryFlag', true);
    }
}
