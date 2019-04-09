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
}