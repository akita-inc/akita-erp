<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MRoleAuths  extends Model
{
    protected $table = "mst_role_auths";

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

    public function getListRolesByCondition($where = array()){
        $mRoleAuth = new MRoleAuths();
        $mRoleAuth = $mRoleAuth->select(DB::raw('mst_role_auths.*,mst_screens.screen_category_id'))
        ->join('mst_screens','mst_screens.id', '=', 'mst_role_auths.mst_screen_id');;

        // 検索条件
        if (isset($where['mst_role_id']) && $where['mst_role_id'] != '')
            $mRoleAuth = $mRoleAuth->where('mst_role_id', "=", $where['mst_role_id']);
        
        return $mRoleAuth->get();
    }
}
