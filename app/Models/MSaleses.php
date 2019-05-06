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

    public function getListByCustomerCd($mst_customers_cd, $mst_business_office_id){
         $query = DB::table('t_saleses')
            ->select(
                't_saleses.*',
                DB::raw("DATE_FORMAT(t_saleses.daily_report_date, '%Y/%m/%d') as daily_report_date"),
                DB::raw("mst_customers.customer_nm_formal as customer_nm"),
                DB::raw('CONCAT_WS("    ",mst_staffs.last_nm,mst_staffs.first_nm) as staff_nm'),
                'mst_vehicles.registration_numbers'
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
            ->where('t_saleses.mst_customers_cd',$mst_customers_cd);
         return $query->get();
    }
}