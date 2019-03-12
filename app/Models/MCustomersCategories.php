<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MCustomersCategories  extends Model
{
    use SoftDeletes;

    protected $table = "mst_customer_categories";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getAllData()
    {
        $data=MCustomersCategories::select('id','data_kb')
            ->orderBy('disp_number','asc')
            ->get();
        $result = array("" => '==選択==');
        foreach (json_decode(json_encode($data), true) as $key=>$item){
            $result[$item['date_id']] = $item['date_nm'];
        }
        return $result;
    }


}
