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
//            DB::raw('mst_business_offices.business_office_nm as applicant_office_id'),
            DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as staff_nm'),
            'wf_approval_status.send_back_reason',
            'wf_approval_status.title'
        )
//            ->leftjoin('mst_general_purposes as expense_app_temporary_payment', function ($join) {
//                $join->on('expense_app_temporary_payment.date_id', '=', 'wf_business_entertaining.approval_kb')
//                    ->where('expense_app_temporary_payment.data_kb', config('params.data_kb.wf_expense_app_temporary_payment'));
//            })
            ->leftjoin('wf_approval_status', function ($join) {
                $join->on('wf_approval_status.wf_id', '=', 'wf_business_entertaining.id')
                    ->where('approval_levels','=',Auth::user()->approval_levels);
            })
//            ->join(DB::raw('mst_business_offices'), function ($join) {
//                $join->on('mst_business_offices.id', '=', 'wf_business_entertaining.applicant_office_id')
//                    ->whereNull('mst_business_offices.deleted_at');
//            })
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'wf_business_entertaining.applicant_id')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->where('wf_business_entertaining.id','=',$id);
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