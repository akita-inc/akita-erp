<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MStaffAuths  extends Model
{
    protected $table = "mst_staff_auths";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getAllData()
    {
        return $this->get();
    }

    public function getStaffAuthByCondition($where = array()){
        $mStaffAuth = new MStaffAuths();
        $mStaffAuth = $mStaffAuth->select("*")
            ->join('mst_screens','mst_screens.id', '=', 'mst_staff_auths.mst_screen_id');;

        // 検索条件
        if (isset($where['mst_staff_id']) && $where['mst_staff_id'] != '')
            $mStaffAuth = $mStaffAuth->where('mst_staff_auths.mst_staff_id', "=", $where['mst_staff_id']);
        if (isset($where['screen_category_id']) && $where['screen_category_id'] != '')
            $mStaffAuth = $mStaffAuth->where('mst_screens.screen_category_id', "=", $where['screen_category_id']);

        return $mStaffAuth->get()->toArray();
    }
}
