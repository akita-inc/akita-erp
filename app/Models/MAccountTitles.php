<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MAccountTitles  extends Model
{
    use SoftDeletes;

    protected $table = "mst_account_titles";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getListAccountTitles()
    {
        $data=$this->select('id','account_title_name')
            ->orderBy('disp_number','asc')
            ->get();
        $result = array("" => '==選択==');
        foreach (json_decode(json_encode($data), true) as $key=>$item){
            $result[$item['id']] = $item['account_title_name'];
        }
        return $result;
    }
}
