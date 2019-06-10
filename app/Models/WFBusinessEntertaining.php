<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WFBusinessEntertaining extends Model {

    protected $table = "wf_business_entertaining";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'modified_at';
    const DELETED_AT = 'delete_at';

    public $label = [];
    public function getInfoForMail($id){
        $query = $this->select(
            'wf_business_entertaining.id',
            'wf_business_entertaining.applicant_id',
            'wf_business_entertaining.applicant_office_id',
            'wf_business_entertaining.date',
            'wf_business_entertaining.client_company_name',
            'wf_business_entertaining.client_members',
            'wf_business_entertaining.client_members_count',
            'wf_business_entertaining.own_members_count',
            'wf_business_entertaining.own_members',
            'wf_business_entertaining.place',
            'wf_business_entertaining.conditions',
            'wf_business_entertaining.purpose',
            DB::raw('CASE WHEN wf_business_entertaining.deposit_flg = 0 THEN "不要"
                           ELSE "要"
                           END AS deposit_flg'),
            DB::raw('CASE WHEN wf_business_entertaining.deposit_flg = 0 THEN ""
                           ELSE CONCAT_WS("","￥",format(wf_business_entertaining.deposit_amount, "#,##0"))
                           END AS deposit_amount')
        )->where('wf_business_entertaining.id','=',$id);
        return $query->first()->toArray();
    }
    public function getInfoByID($id){
        return $this->select(
            'wf_business_entertaining.*',
            'mst_staffs.mail',
            DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as staff_nm'),
            DB::raw('mst_business_offices.business_office_nm as applicant_office_nm')
        )
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'wf_business_entertaining.applicant_id')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->join('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'wf_business_entertaining.applicant_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->where('wf_business_entertaining.id','=',$id)
            ->first();
    }

}