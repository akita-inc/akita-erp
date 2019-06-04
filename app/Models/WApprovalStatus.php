<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WApprovalStatus extends Model {
    //use SoftDeletes;

    protected $table = "wf_approval_status";

    const CREATED_AT = null;
    const UPDATED_AT = null;
    const DELETED_AT = null;

    public $label = [];

    public function getListByWfID($wf_id,$wf_type_id){
        return $this->select(
                'wf_approval_status.approval_levels',
                'wf_approval_status.title',
                DB::raw('(CASE WHEN wf_approval_status.approval_kb=2 THEN mst_general_purposes.contents1 ELSE mst_general_purposes.date_nm END) as status'),
                DB::raw("DATE_FORMAT(wf_approval_status.approval_date, '%Y/%m/%d %H:%i') as approval_date"),
                'wf_approval_status.send_back_reason'
            )
            ->leftjoin('mst_general_purposes', function ($join) {
                $join->on('mst_general_purposes.date_id', '=', 'wf_approval_status.approval_fg')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb.wf_approval_status'));
            })
            ->where('wf_id','=',$wf_id)
            ->where('wf_type_id','=',$wf_type_id)
            ->orderBy('wf_approval_status.approval_steps')
            ->get();

    }

    public function countVacationNotApproval($wf_id,$wf_type_id, $flag_is_user_login=null){
         $query = $this->where('wf_id','=',$wf_id)
                ->where('wf_type_id','=',$wf_type_id)
                ->where('approval_fg','=',0);
         if(!is_null($flag_is_user_login) && $flag_is_user_login){
             $query = $query->where('approval_levels','=',Auth::user()->approval_levels);
         }
        return $query->count();
    }

    public function approvalVacation($wf_id,$wf_type_id,$currentTime){
        return $this->where('wf_id','=',$wf_id)
                ->where('wf_type_id','=',$wf_type_id)
                ->where('approval_levels','=',Auth::user()->approval_levels)
                ->update([
                    'approver_id' => Auth::user()->staff_cd,
                    'approval_fg' => 1,
                    'approval_date' => $currentTime,
                ]);
    }

    public function rejectVacation($wf_id,$wf_type_id,$currentTime,$send_back_reason ){
        $this->where('wf_id','=',$wf_id)
            ->where('wf_type_id','=',$wf_type_id)
            ->where('approval_fg','=',0)
            ->where('approval_levels','=',Auth::user()->approval_levels)
            ->update([
                'send_back_reason' => $send_back_reason,
                'approver_id' => Auth::user()->staff_cd,
                'approval_fg' => 2,
                'approval_date' => $currentTime,
            ]);
        $this->where('wf_id','=',$wf_id)
            ->where('wf_type_id','=',$wf_type_id)
            ->where('approval_fg','=',0)
            ->update([
                'approver_id' => Auth::user()->staff_cd,
                'approval_fg' => 2,
                'approval_date' => $currentTime,
            ]);
    }

    public function getMinStepsLevel($wf_id,$wf_type_id){
        return $this->select(
                'approval_levels'
            )
            ->where('wf_id','=',$wf_id)
            ->where('wf_type_id','=',$wf_type_id)
            ->where('approval_fg','=',0)
            ->orderBy('approval_steps','ASC')
            ->first();
    }
}