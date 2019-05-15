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

    public function getInvoicePDFDetail($listID,$dataSearch){
        $date = date("m/d",strtotime($dataSearch['billing_month'].'/'.($dataSearch['special_closing_date'] ? $dataSearch['closed_date_input'] : $dataSearch['closed_date'])));

        $query1 = DB::table('t_billing_history_header_details as details')->select(
                DB::raw("'$date' as daily_report_date"),
                'details.goods',
                DB::raw("(CASE 
                    WHEN mst_vehicles.vehicle_size_kb=1 THEN '2t' 
                    WHEN mst_vehicles.vehicle_size_kb=2 THEN '4t' 
                    WHEN mst_vehicles.vehicle_size_kb=3 THEN '10t' 
                    END) as size"),
                DB::raw('SUM(IFNULL(details.quantity,0)) as quantity'),
                DB::raw('format(details.unit_price, "#,##0") as unit_price'),
                DB::raw("(SUM(IFNULL(details.quantity,0))*IFNULL(details.unit_price,0)) as amount"),
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
            ->where('summary_indicator','=',1)
            ->groupBy('details.transport_cd','details.goods','details.unit_price','details.departure_point_name', 'details.landing_name', 'details.delivery_destination','details.staff_cd', 'details.vehicles_cd', 'details.tax_classification_flg','mst_vehicles.vehicle_size_kb','details.departure_point_name', 'details.landing_name', 'details.delivery_destination','mst_staffs.last_nm');
        $result1 = $query1->get()->toArray();
        $query2 = DB::table('t_billing_history_header_details as details')->select(
            DB::raw("DATE_FORMAT(details.daily_report_date, '%m/%d') as daily_report_date"),
            'details.goods',
            DB::raw("(CASE 
                    WHEN mst_vehicles.vehicle_size_kb=1 THEN '2t' 
                    WHEN mst_vehicles.vehicle_size_kb=2 THEN '4t' 
                    WHEN mst_vehicles.vehicle_size_kb=3 THEN '10t' 
                    END) as size"),
            DB::raw('IFNULL(details.quantity,0) as quantity'),
            DB::raw('format(details.unit_price, "#,##0") as unit_price'),
            DB::raw("(IFNULL(details.quantity,0)*IFNULL(details.unit_price,0)) as amount"),
            'details.departure_point_name',
            'details.landing_name',
            DB::raw('IFNULL(details.loading_fee,0) as loading_fee'),
            DB::raw('IFNULL(details.wholesale_fee,0) as wholesale_fee'),
            DB::raw('IFNULL(details.incidental_fee,0) as incidental_fee'),
            DB::raw('IFNULL(details.waiting_fee,0) as waiting_fee'),
            DB::raw('IFNULL(details.surcharge_fee,0) as surcharge_fee'),
            DB::raw('IFNULL(details.billing_fast_charge,0) as billing_fast_charge'),
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
            ->where('summary_indicator','<>',1);
        $result2 = $query2->get()->toArray();
        $result = array_merge($result1,$result2);
        usort($result, function ($a, $b){
            return strtotime($a->daily_report_date) - strtotime($b->daily_report_date);
        });
        return $result;
    }

    public function getAmazonCSVContent($listID){
        $query = DB::table('t_billing_history_header_details as details')
            ->select(
                DB::raw("DATE_FORMAT(details.daily_report_date, '%m/%d') as daily_report_date"),
                'details.goods',
                DB::raw("(CASE 
                        WHEN mst_vehicles.vehicle_size_kb=1 THEN '2トン' 
                        WHEN mst_vehicles.vehicle_size_kb=2 THEN '4トン' 
                        WHEN mst_vehicles.vehicle_size_kb=3 THEN '10トン' 
                        END) as size"),
                DB::raw('IFNULL(details.quantity,0) as quantity'),
                DB::raw('IFNULL(details.unit_price,0) as unit_price'),
                DB::raw('IFNULL(details.total_fee, 0) as total_fee'),
                'details.departure_point_name',
                'details.landing_name',
                DB::raw('IFNULL(details.loading_fee,0) as loading_fee'),
                DB::raw('IFNULL(details.wholesale_fee,0) as wholesale_fee'),
                DB::raw('IFNULL(details.incidental_fee,0) as incidental_fee'),
                DB::raw('IFNULL(details.waiting_fee,0) as waiting_fee'),
                DB::raw('IFNULL(details.surcharge_fee,0) as surcharge_fee'),
                DB::raw('IFNULL(details.billing_fast_charge,0) as billing_fast_charge'),
                'details.delivery_destination'
            )
            ->leftjoin('mst_vehicles', function ($join) {
                $join->on('mst_vehicles.vehicles_cd', '=', 'details.vehicles_cd')
                    ->whereNull('mst_vehicles.deleted_at');
            })
            ->whereNull('details.deleted_at')
            ->whereIn('details.id',$listID);
       return $query->get();
    }
}