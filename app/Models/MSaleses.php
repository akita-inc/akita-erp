<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MSaleses extends Model {
    use SoftDeletes;

    protected $table = "t_saleses";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getListByCustomerCd($mst_customers_cd, $mst_business_office_id,$dataSearch){
         $query = DB::table('t_saleses')
            ->select(
                't_saleses.*',
                DB::raw("DATE_FORMAT(t_saleses.daily_report_date, '%Y/%m/%d') as daily_report_date"),
                DB::raw("mst_customers.customer_nm_formal as customer_nm"),
                DB::raw('CONCAT_WS("    ",mst_staffs.last_nm,mst_staffs.first_nm) as staff_nm'),
                'mst_vehicles.registration_numbers'
            )
             ->join(DB::raw("
                    (
                    SELECT
                        connect_sales.id,
                        connect_sales.mst_customers_cd sales_cus_cd,
                        connect_sales.customer_nm_formal sales_cus_nm,
                        bill_info.mst_customers_cd bill_cus_cd,
                        bill_info.customer_nm_formal bill_cus_nm, -- ↓
                        bill_info.consumption_tax_calc_unit_id,
                        bill_info.rounding_method_id
                    FROM
                        mst_customers connect_sales
                    JOIN mst_customers bill_info ON IFNULL(
                        connect_sales.bill_mst_customers_cd,
                        connect_sales.mst_customers_cd
                    ) = bill_info.mst_customers_cd
                WHERE
                    connect_sales.deleted_at IS NULL
                    AND bill_info.deleted_at IS NULL
                ) c
             "),'t_saleses.mst_customers_cd', '=', 'c.sales_cus_cd'

             )
             ->leftjoin('mst_customers', function ($join) {
                 $join->on('mst_customers.mst_customers_cd', '=', 't_saleses.mst_customers_cd')
                     ->whereNull('mst_customers.deleted_at');
             })
             ->leftjoin('mst_staffs', function ($join) {
                 $join->on('mst_staffs.staff_cd', '=', 't_saleses.staff_cd')
                     ->whereNull('mst_staffs.deleted_at');
             })
             ->leftjoin('mst_vehicles', function ($join) {
                 $join->on('mst_vehicles.vehicles_cd', '=', 't_saleses.vehicles_cd')
                     ->whereNull('mst_vehicles.deleted_at');
             })
            ->whereNull('t_saleses.deleted_at')
            ->where('t_saleses.mst_business_office_id',$mst_business_office_id)
            ->where('c.bill_cus_cd',$mst_customers_cd);
        if ($dataSearch['billing_year'] != '' && $dataSearch['billing_month'] != '' && ($dataSearch['closed_date_input'] !='' || $dataSearch['closed_date'])) {
            $date = date("Y-m-d",strtotime($dataSearch['billing_year'].'/'.$dataSearch['billing_month'].'/'.($dataSearch['special_closing_date'] ? $dataSearch['closed_date_input'] : $dataSearch['closed_date'])));
            $query = $query->where('t_saleses.daily_report_date','<=',$date);
        }
         return $query->orderBy('t_saleses.daily_report_date')->get();
    }
}