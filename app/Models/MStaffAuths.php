<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
    public function getDataByCondition($screen_category_id){
        $data =  $this
            ->select('accessible_kb')
            ->join('mst_screens','mst_screens.id', '=', 'mst_staff_auths.mst_screen_id')
            ->where('mst_staff_id','=',Auth::user()->id)
            ->where('mst_screens.screen_category_id','=',$screen_category_id)
            ->get();
        return $data;
    }
    public function getDataScreenStaffAuth(){
        $result = [];
        $data =  $this
            ->select('mst_staff_auths.mst_screen_id as mst_screen_auth')
            ->join('mst_screens','mst_screens.id', '=', 'mst_staff_auths.mst_screen_id')
            ->where('mst_staff_id','=',Auth::user()->id)
            ->where('mst_screens.screen_category_id','=',1)
            ->get();
        foreach (json_decode(json_encode($data), true) as $key => $item) {
            $result[$key] = $item['mst_screen_auth'];
        }
        return $result;
    }
}
