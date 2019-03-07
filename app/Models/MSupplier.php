<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MSupplier extends Model
{
    use SoftDeletes;

    protected $table = "mst_suppliers";
    const UPDATED_AT = "modified_at";

    public $label = [

    ];

    public $rules = [

    ];

    public function getSuppliers($where = array()){
        $suppliers = new MSupplier();
        $suppliers = $suppliers->select(DB::raw('mst_suppliers.*'))
                                ->addselect('mst_general_purposes.date_nm');
        $suppliers = $suppliers->leftjoin('mst_general_purposes', function ($join) {
            $join->on('mst_general_purposes.date_id', '=', 'mst_suppliers.prefectures_cd')
                    ->where('mst_general_purposes.data_kb', config('params.data_kb')['prefecture']);
        });

        // 検索条件
        if (isset($where['suppliers_cd']) && $where['suppliers_cd'] != '')
            $suppliers = $suppliers->where('mst_suppliers_cd', "LIKE", "%{$where['suppliers_cd']}%");
        if (isset($where['supplier_nm']) && $where['supplier_nm'] != '')
            $suppliers = $suppliers->where('supplier_nm', "LIKE", "%{$where['supplier_nm']}%");
        if (isset($where['reference_date']) && $where['reference_date'] != '') {
            $suppliers = $suppliers->where('adhibition_start_dt', "<=", $where['reference_date']);
            $suppliers = $suppliers->where('adhibition_end_dt', ">=", $where['reference_date']);
        }

        $suppliers->orderBy('mst_suppliers_cd', 'adhibition_start_dt');

        return $suppliers->paginate(config("params.page_size"));
    }

}
