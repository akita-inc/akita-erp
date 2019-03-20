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
        dd($data);
        return Response()->json(array('success'=>true,'data'=>$data));
    }
    public function getStaffJobEx($id)
    {
        $listStaffJobEx = DB::table("mst_staff_job_experiences")
            ->select(
                "id",
                "job_duties",
                "staff_tenure_start_dt",
                "staff_tenure_end_dt"            )
            ->where("mst_staff_id","=",$id)
            ->where("deleted_at",null)
            ->orderBy(DB::raw("disp_number*1"))
            ->get();
        if($listStaffJobEx){
            $listStaffJobEx->toArray();
        }
        return Response()->json(array('success'=>true, 'data'=> $listStaffJobEx));
    }
    public function getStaffQualifications($id)
    {
        $data = DB::table("mst_staff_qualifications")
            ->select(
                "id",
                "qualification_kind_id",
                "acquisition_dt",
                "period_validity_start_dt",
                "period_validity_end_dt",
                "amounts",
                "notes as qualifications_notes",
                "payday"
            )
            ->where("mst_staff_id","=",$id)
            ->where("deleted_at",null)
            ->orderBy(DB::raw("disp_number*1"))
            ->get();
        if($data){
            $data->toArray();
        }
        return Response()->json(array('success'=>true, 'data'=> $data));
    }
    public function getStaffDependents($id)
    {
        $data = DB::table("mst_staff_dependents")
            ->select(
                "id",
                "dependent_kb as dept_dependent_kb",
                "last_nm as dept_last_nm",
                "last_nm_kana as dept_last_nm_kana",
                "first_nm as dept_first_nm",
                "first_nm_kana as dept_first_nm_kana",
                "birthday as dept_birthday",
                "sex_id as dept_sex_id",
                "social_security_number as dept_social_security_number"
            )
            ->where("mst_staff_id","=",$id)
            ->where("deleted_at",null)
            ->orderBy("id","ASC")
            ->get();
        if($data){
            $data->toArray();
        }
        return Response()->json(array('success'=>true, 'data'=> $data));
    }
    public function getStaffAuths($id)
    {
        $data = DB::table("mst_staff_auths")
            ->select(DB::raw('mst_staff_auths.*, mst_screens.screen_category_id'))
            ->join('mst_screens','mst_screens.id', '=', 'mst_staff_auths.mst_screen_id')
            ->where("mst_staff_id","=",$id)
            ->get();
        if($data){
            $data->toArray();
            $data = $data->groupBy('screen_category_id');
        }
        return Response()->json(array('success'=>true, 'data'=> $data));

    }
}
