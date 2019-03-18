<?php
/**
 * Created by PhpStorm.
 * User: ptson
 * Date: 03/15/2019
 * Time: 2:54 PM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MScreens extends Model
{
    protected $table = "mst_screens";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getListScreensByCondition($where = array()){
        $mScreen = new MScreens();
        $mScreen = $mScreen->select(DB::raw('mst_screens.*'));

        // 検索条件
        if (isset($where['screen_category_id']) && $where['screen_category_id'] != '')
            $mScreen = $mScreen->where('screen_category_id', "=", $where['screen_category_id']);

        $mScreen->orderBy('disp_number');

        return $mScreen->get();
    }
}
