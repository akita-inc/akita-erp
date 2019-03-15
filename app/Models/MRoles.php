<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class MRoles  extends Model
{
    protected $table = "mst_roles";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getAllData()
    {
        return $this->where('deleted_at','=',null)
                    ->get();
    }

    public function getListRoles()
    {
        $data = $this->select('id', 'role_nm')
            ->orderBy('disp_number', 'asc')
            ->get();
        $result = array("" => '==選択==');
        foreach (json_decode(json_encode($data), true) as $key => $item) {
            $result[$item['id']] = $item['role_nm'];
        }
        return $result;
    }
}
