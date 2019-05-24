<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WPaidVacation extends Model {
//    use SoftDeletes;

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
                'wf_paid_vacation.days',
                'wf_paid_vacation.times',
                'wf_paid_vacation.reasons'
            )
            ->leftjoin('mst_general_purposes as vacation_indicator', function ($join) {
                $join->on('vacation_indicator.date_id', '=', 'wf_paid_vacation.approval_kb')
                    ->where('vacation_indicator.data_kb', config('params.data_kb.vacation_indicator'));
            })
            ->join(DB::raw('mst_business_offices'), function ($join) {
                $join->on('mst_business_offices.id', '=', 'wf_paid_vacation.applicant_office_id')
                    ->whereNull('mst_business_offices.deleted_at');
            })
            ->where('wf_paid_vacation.id','=',$id);
    return $query->first()->toArray();

    }

    public function getInfoByID($id){
        return $this->select(
                'wf_paid_vacation.*',
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