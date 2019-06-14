<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WFBusinessEntertainingExpenses extends Model {

    protected $table = "wf_business_entertaining_expenses";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'modified_at';
    const DELETED_AT = 'delete_at';

    public $label = [];
    public function getInfoForMail($id){
        $query = $this->select(
            'wf_business_entertaining_expenses.id',
            DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as applicant_id'),
            DB::raw('mst_business_offices.business_office_nm as applicant_office_id'),
            'wf_business_entertaining_expenses.wf_business_entertaining_id',
            DB::raw("DATE_FORMAT(wf_business_entertaining_expenses.date, '%Y/%m/%d') as date"),
            'wf_business_entertaining_expenses.client_company_name',
            'wf_business_entertaining_expenses.client_members',
            'wf_business_entertaining_expenses.client_members_count',
            'wf_business_entertaining_expenses.own_members_count',
            'wf_business_entertaining_expenses.own_members',
            'wf_business_entertaining_expenses.place',
            'wf_business_entertaining_expenses.report',
            DB::raw('CONCAT_WS("","￥",format(wf_business_entertaining_expenses.cost, "#,##0")) AS cost'),
            DB::raw('CONCAT_WS("","￥",format(wf_business_entertaining_expenses.deposit_amount, "#,##0")) AS deposit_amount'),
            DB::raw('CONCAT_WS("","￥",format(wf_business_entertaining_expenses.payoff_amount, "#,##0")) AS payoff_amount'),
            'wf_approval_status.send_back_reason',
            'wf_approval_status.title'
        )
        ->leftjoin('wf_approval_status', function ($join) {
            $join->on('wf_approval_status.wf_id', '=', 'wf_business_entertaining_expenses.id')
                ->where('approval_levels','=',Auth::user()->approval_levels);
        })
        ->join(DB::raw('mst_business_offices'), function ($join) {
            $join->on('mst_business_offices.id', '=', 'wf_business_entertaining_expenses.applicant_office_id')
                ->whereNull('mst_business_offices.deleted_at');
        })
        ->leftjoin('mst_staffs', function ($join) {
            $join->on('mst_staffs.staff_cd', '=', 'wf_business_entertaining_expenses.applicant_id')
                ->whereNull('mst_staffs.deleted_at');
        })
        ->where('wf_business_entertaining_expenses.id','=',$id);
        return $query->first()->toArray();
    }
    public function getInfoByID($id){
        return $this->select(
            'wf_business_entertaining_expenses.*',
            'mst_staffs.mail',
            DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as staff_nm'),
            DB::raw('mst_business_offices.business_office_nm as applicant_office_nm')
        )
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'wf_business_entertaining_expenses.applicant_id')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->join('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'wf_business_entertaining_expenses.applicant_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->where('wf_business_entertaining_expenses.id','=',$id)
            ->first();
    }

}