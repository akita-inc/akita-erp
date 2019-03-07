<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Common;
use App\Models\MSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SuppliersController
{
    public function convertToKana(Request $request)
    {
        $data = $request->get('data');
        $string = Common::convertToKana($data,'katakana');
        return Response()->json(array('success'=>true,'info'=>$string));
    }
}