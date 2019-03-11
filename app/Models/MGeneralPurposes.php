<?php
/**
 * Created by PhpStorm.
 * User: tinhnv
 * Date: 5/5/2017
 * Time: 3:22 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MGeneralPurposes extends Model
{
    protected $table = "mst_general_purposes";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';
    public $label = [

    ];

    public $rules = [

    ];
    public function validate($data){
        return Validator::make($data, $this->rules, Lang::get('validation'), $this->label);
    }
    public function getMConfigByConditions($conditions, $arrSelectFields = [],$orders=null){
        $query = DB::table($this->table);
        foreach ($conditions as $condition){
            if($condition['field']=='data_kb' && $condition['value']==1){
                $query = $query->where('disp_fg', '=', 1);
            }
            $query = $query->where($condition['field'], $condition['operator'], $condition['value']);
        }
	    $query = $query->where('deleted_at', '=', null);
        if(isset($orders))
        {
            foreach ($orders as $order){
                $query = $query->orderBy($order['field'],$order['sortby']);
            }
        }
        if (is_array($arrSelectFields) && count($arrSelectFields) > 0){
            return $query->get($arrSelectFields);
        }
        if (is_array($arrSelectFields) && count($arrSelectFields) == 0){
            return $query->get();
        }
        if (is_string($arrSelectFields)){
            return $query->get(array($arrSelectFields));
        }
    }

    public function getInfoByMngDiv($data_kb,$kDefault = "default",$order=[]){
        $data = $this->getMConfigByConditions(
            array(
                [
                    'field' => 'data_kb',
                    'operator' => '=',
                    'value' => $data_kb
                ]
            ),
            array('id', 'info_1','date_id'),
            $order
        );
        $result = array($kDefault => '==選択==');

        foreach (json_decode(json_encode($data), true) as $key=>$item){
            $result[$item['date_id']] = $item['info_1'];
        }
        return $result;
    }

    public function getDateIDByDataKB($data_kb,$kDefault = "default"){
        $data = $this->getMConfigByConditions(
            array(
                [
                    'field' => 'data_kb',
                    'operator' => '=',
                    'value' => $data_kb
                ]
            ),
            array('date_id', 'date_nm'),
			array(
				[
					'field' => 'disp_number',
					'sortby' => 'asc',
				]
			)
        );
        if($kDefault == "Empty"){
	        $result = array();
        }else{
	        $result = array($kDefault => '==選択==');
        }
        foreach (json_decode(json_encode($data), true) as $key=>$item){
            $result[$item['date_id']] = $item['date_nm'];
        }
        return $result;
    }
    public function getDataByMngDiv($div){
        return $this->where('data_kb',$div)
            ->where('deleted_at','=',null)
            ->orderBy('disp_number','ASC')
            ->get();
    }
    public function getDataByMngDivDESC($div){
        return $this->where('data_kb',$div)
            ->where('deleted_at','=',null)
            ->orderBy('disp_number')
            ->get();
    }
    public function getDataByInfo1AndMngDiv($info_1,$div){
        return $this->where('data_kb',$div)
            ->where('info_1', 'LIKE', $info_1.'%')
            ->orderBy('disp_number','ASC')
            ->get();
    }

    public function getDataByDivAndCd($div, $date_id){
        return $this->where('data_kb',$div)
            ->where('deleted_at','=',null)
            ->get()->first();
    }

    public function getAllData($data, $isPaging = true){
        $return = $this->select(array('data_kb','data_kb_nm'))
            ->where('deleted_at','=',null)
            ->groupBy('data_kb');
        if($isPaging){
            $return = $return->paginate(config("params.page_size"));
        }
        return $return->appends($data);
    }

    public function getIdMax($column = 'data_kb'){
        $result = $this->max($column);
        return $result+1;
    }

    public function getOrderMin($data_kb){
        $result = $this->select(DB::raw('min(disp_number)'))
            ->where('data_kb','=',$data_kb)
            ->where('deleted_at','=',null)
            ->first();
        return $result->toArray()['min(disp_number)'];
    }

    public function getOrderMax($data_kb){
        $result = $this->select(DB::raw('max(disp_number)'))
            ->where('data_kb','=',$data_kb)
            ->where('deleted_at','=',null)
            ->first();
        return $result->toArray()['max(disp_number)'];
    }

    public function getMngCdMaxByDiv($data_kb){
        $result = $this->select(DB::raw('max(date_id)'))
            ->where('data_kb','=',$data_kb)
            ->where('deleted_at','=',null)
            ->first();
        return $result->toArray()['max(date_id)'] + 1;
    }
}