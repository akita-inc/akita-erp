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

trait FormTrait
{
    public $query = null;

    protected function getQuery(){
        $this->query = DB::table($this->table);
    }

    public function validForm(Request $request)
    {
        $data = $request->all();
        $data['province_id'] = $request->province_name['id'];
        $validator = Validator::make($data, [
            'name' => 'required',
            'type' => 'required',
            'province_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success'=>FALSE,
                'message'=> $validator->errors()
            ]);
        }else{
            return response()->json([
                'success'=>true,
                'message'=> []
            ]);
        }
    }
}
