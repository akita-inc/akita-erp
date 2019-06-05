<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MPurchases extends Model {
    use SoftDeletes;

    protected $table = "t_purchases";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getAccountsPayableData($fieldSearch){
        $query = $this->select(
                't_purchases.mst_suppliers_cd',
                'mst_suppliers.supplier_nm',
                DB::raw("SUM(IFNULL(t_purchases.tax_included_amount,0)) as purchases_tax_included_amount"),
                DB::raw("SUM(IFNULL(t_saleses.tax_included_amount,0)) as saleses_tax_included_amount")
            )
            ->leftjoin('mst_suppliers', function ($join) {
                $join->on('t_purchases.mst_suppliers_cd', '=', 'mst_suppliers.mst_suppliers_cd')
                    ->whereNull('mst_suppliers.deleted_at');
            })
            ->leftjoin('t_saleses', function ($join) {
                $join->on('t_saleses.document_no', '=', 't_purchases.document_no')
                    ->on('t_saleses.branch_office_cd','=','t_purchases.branch_office_cd')
                    ->on('t_saleses.daily_report_date','=','t_purchases.daily_report_date')
                    ->whereNull('t_saleses.deleted_at');
            })
            ->whereNull('t_purchases.deleted_at')
            ->where('t_purchases.daily_report_date','>=',$fieldSearch['start_date'])
            ->where('t_purchases.daily_report_date','<=',$fieldSearch['end_date'])
            ->groupBy(
                't_purchases.mst_suppliers_cd',
                'mst_suppliers.supplier_nm'
            )
            ->orderBy('t_purchases.mst_suppliers_cd' );
        return $query->get();
    }
}
