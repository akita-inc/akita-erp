<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Common;
use App\Models\MSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Http\Controllers\TraitRepositories\FormTrait;

class SuppliersController
{
    use ListTrait, FormTrait;
    public function convertToKana(Request $request)
    {
        $data = $request->get('data');
        $string = Common::convertToKana($data,'katakana');
        return Response()->json(array('success'=>true,'info'=>$string));
    }

    public function checkIsExist(Request $request, $id){
        $mode = $request->get('mode');
        $mSupplier = new MSupplier();
        $mSupplier = $mSupplier->find($id);
        if (isset($mSupplier)) {
            return Response()->json(array('success'=>true));
        } else {
            return Response()->json(array('success'=>false, 'msg'=> is_null($mode) ? Lang::trans('messages.MSG04004') : Lang::trans('messages.MSG04001')));
        }
    }
}