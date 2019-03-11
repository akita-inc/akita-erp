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
use Illuminate\Validation\Validator;

trait FormTrait
{
    public $ruleValid = [];
    public $messagesCustom = [];

    public function validForm(Request $request)
    {
        $data = $request->all();
        if( !empty($this->ruleValid) ){
            $validator = Validator::make( $data, $this->ruleValid,$this->messagesCustom );
            if ( $validator->fails() ) {
                return response()->json([
                    'success'=>FALSE,
                    'message'=> $validator->errors()
                ]);
            }
        }
        return response()->json([
            'success'=>true,
            'message'=> []
        ]);
    }
}
