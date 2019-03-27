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
    public function getDataByCondition($screen_category_id){
        $data =  $this
            ->select('accessible_kb')
            ->join('mst_screens','mst_screens.id', '=', 'mst_staff_auths.mst_screen_id')
            ->where('mst_staff_id','=',Auth::user()->id)
            ->where('mst_screens.screen_category_id','=',$screen_category_id)
            ->get();
        return $data;
    }
}
