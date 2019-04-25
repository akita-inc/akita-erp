<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MBusinessOffices  extends Model
{
    use SoftDeletes;

    protected $table = "mst_business_offices";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];
    public function getAllData()
    {
        return $this->select('id', 'business_office_nm')
            ->where('deleted_at','=',null)
            ->orderBy('disp_number', 'asc')
            ->get();
    }
    public function getMstBusinessOffice()
    {
        $result=array();
        $data=$this->select('id','mst_business_office_cd')
            ->where('deleted_at','=',null)
            ->orderBy('disp_number', 'asc')
            ->get();
        foreach($data as $key=>$value)
        {
            $val = json_decode( json_encode($value), true);
            $result[$val['mst_business_office_cd']]=$val['id'];
        }
        return $result;
    }
    public function getListBusinessOffices()
    {
        $data = $this->select('id', 'business_office_nm')
            ->where('deleted_at','=',null)
            ->orderBy('disp_number', 'asc')
            ->get();
        $result = array("" => '==選択==');
        foreach (json_decode(json_encode($data), true) as $key => $item) {
            $result[$item['id']] = $item['business_office_nm'];
        }
        return $result;
    }
}
