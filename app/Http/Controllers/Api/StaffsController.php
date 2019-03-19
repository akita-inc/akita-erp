<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Common;
use App\Models\MRoleAuths;
use App\Models\MStaffs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\MGeneralPurposes;
use App\Http\Controllers\TraitRepositories\ListTrait;
use App\Http\Controllers\TraitRepositories\FormTrait;

class StaffsController
{
    use ListTrait, FormTrait;
    public function getListReMunicipalOffice()
    {
        $mGeneralPurposes = new MGeneralPurposes();
        $data=$mGeneralPurposes->getNmByDataKB(config('params.data_kb')['relocation_municipal_office_cd'],'');
        return Response()->json(array('success'=>true,'data'=>$data));
    }

    public function getRoleConfig(Request $request){
        $mRoleAuth = new MRoleAuths();
        $data = $mRoleAuth->getListRolesByCondition(['mst_role_id'=>$request->get('role_id')]);
        return Response()->json(array('success'=>true,'data'=>$data));
    }
}