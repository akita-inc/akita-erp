<?php
/**
 * Created by PhpStorm.
 * User: TINHNV
 * Date: 7/3/2018
 * Time: 10:13 AM
 */

namespace App\Http\Controllers\TraitRepositories;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function backHistory(){
        Session::put('backQueryFlag', true);
    }
}
