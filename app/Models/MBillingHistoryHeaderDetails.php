<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MBillingHistoryHeaderDetails extends Model {
    use SoftDeletes;

    protected $table = "t_billing_history_header_details";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getInvoicePDFDetail($listID){
        $query = DB::table('t_billing_history_header_details as details')->select(
                DB::raw("DATE_FORMAT(details.daily_report_date, '%m/%d') as daily_report_date"),
                'details.goods',
                DB::raw("(CASE 
                    WHEN mst_vehicles.vehicle_size_kb=1 THEN '2t' 
                    WHEN mst_vehicles.vehicle_size_kb=2 THEN '4t' 
                    WHEN mst_vehicles.vehicle_size_kb=3 THEN '10t' 
                    END) as size"),
                DB::raw('SUM(IFNULL(details.quantity,0)) as quantity'),
                DB::raw('format(details.unit_price, "#,##0") as unit_price'),
                DB::raw("(SUM(IFNULL(details.quantity,0))*details.unit_price) as amount"),
                'details.departure_point_name',
                'details.landing_name',
                 DB::raw('SUM(IFNULL(details.loading_fee,0)) as loading_fee'),
                 DB::raw('SUM(IFNULL(details.wholesale_fee,0)) as wholesale_fee'),
                 DB::raw('SUM(IFNULL(details.incidental_fee,0)) as incidental_fee'),
                 DB::raw('SUM(IFNULL(details.waiting_fee,0)) as waiting_fee'),
                 DB::raw('SUM(IFNULL(details.surcharge_fee,0)) as surcharge_fee'),
                 DB::raw('SUM(IFNULL(details.billing_fast_charge,0)) as billing_fast_charge'),
                'details.delivery_destination',
                DB::raw("(mst_staffs.last_nm) as staff_nm")
            )
            ->leftjoin('mst_staffs', function ($join) {
                $join->on('mst_staffs.staff_cd', '=', 'details.staff_cd')
                    ->whereNull('mst_staffs.deleted_at');
            })
            ->leftjoin('mst_vehicles', function ($join) {
                $join->on('mst_vehicles.vehicles_cd', '=', 'details.vehicles_cd')
                    ->whereNull('mst_vehicles.deleted_at');
            })
            ->whereNull('details.deleted_at')
            ->whereIn('details.id',$listID)
            ->groupBy('details.transport_cd','details.goods','details.unit_price','details.departure_point_name', 'details.landing_name', 'details.delivery_destination','details.staff_cd', 'details.vehicles_cd', 'details.tax_classification_flg','details.daily_report_date','mst_vehicles.vehicle_size_kb','details.departure_point_name', 'details.landing_name', 'details.delivery_destination','mst_staffs.last_nm');
        return $query->get();
    }
}