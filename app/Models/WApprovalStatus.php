<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class WApprovalStatus extends Model {
    //use SoftDeletes;

    protected $table = "wf_approval_status";

    /*const CREATED_AT = 'create_at';
    const UPDATED_AT = 'modified_at';
    const DELETED_AT = 'delete_at';*/

    public $label = [];

    public function getListByWfID($wf_id){
        return $this->select(
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
            ->where('wf_type_id','=',1)
            ->orderBy('wf_approval_status.approval_steps')
            ->get();

    }

    public function countVacationNotApproval($wf_id){
        return $this->where('wf_id','=',$wf_id)
                ->where('wf_type_id','=',1)
                ->where('approval_fg','=',0)
                ->count();
    }
}