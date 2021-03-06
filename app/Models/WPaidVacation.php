<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WPaidVacation extends Model {

    protected $table = "wf_paid_vacation";

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'modified_at';
    const DELETED_AT = 'delete_at';

    public $label = [];

    public function getInfoForMail($id){
        $query = $this->select(
                'wf_paid_vacation.id',
                'wf_paid_vacation.applicant_id',
                DB::raw('vacation_indicator.date_nm as approval_kb'),
                DB::raw('mst_business_offices.business_office_nm as applicant_office_id'),
                DB::raw("DATE_FORMAT(wf_paid_vacation.start_date, '%Y/%m/%d') as start_date"),
                DB::raw("DATE_FORMAT(wf_paid_vacation.end_date, '%Y/%m/%d') as end_date"),
                DB::raw("(CASE WHEN wf_paid_vacation.days > 0 THEN CONCAT(wf_paid_vacation.days, ' 日')  ELSE '' END) as days"),
                DB::raw("(CASE WHEN wf_paid_vacation.times > 0 THEN CONCAT(wf_paid_vacation.times, ' 時間')  ELSE '' END) as times"),
                DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as staff_nm'),
                'wf_paid_vacation.reasons',
                'wf_approval_status.send_back_reason',
                'wf_approval_status.title'
            )
            ->leftjoin('mst_general_purposes as vacation_indicator', function ($join) {
                $join->on('vacation_indicator.date_id', '=', 'wf_paid_vacation.approval_kb')
                    ->where('vacation_indicator.data_kb', config('params.data_kb.vacation_indicator'));
            })
            ->leftjoin('wf_approval_status', function ($join) {
                $join->on('wf_approval_status.wf_id', '=', 'wf_paid_vacation.id')
                    ->where('approval_levels','=',Auth::user()->approval_levels);
            })
            ->join(DB::raw('mst_business_offices'), function ($join) {
                $join->on('mst_business_offices.id', '=', 'wf_paid_vacation.applicant_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'wf_paid_vacation.applicant_id')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->where('wf_paid_vacation.id','=',$id);
    return $query->first()->toArray();

    }

    public function getInfoByID($id){
        return $this->select(
                'wf_paid_vacation.*',
                'mst_staffs.mail',
                DB::raw('CONCAT(mst_staffs.last_nm , mst_staffs.first_nm) as staff_nm'),
                DB::raw('mst_business_offices.business_office_nm as applicant_office_nm')
            )
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'wf_paid_vacation.applicant_id')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->join('mst_business_offices', function ($join) {
                $join->on('mst_business_offices.id', '=', 'wf_paid_vacation.applicant_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->where('wf_paid_vacation.id','=',$id)
            ->first();
    }
}