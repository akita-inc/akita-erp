<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TPurchases extends Model {
    use SoftDeletes;

    protected $table = "t_purchases";

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    public $label = [
    ];

    public $rules = [

    ];

    public function getListBySupplierCdAndBusinessOfficeIdDailyReportDate($mst_suppliers_cd, $mst_business_office_id, $daily_report_date){
        $daily_report_date = date('Y-m-d',strtotime($daily_report_date));
        $query = DB::table('t_purchases')
            ->select(
                DB::raw("DATE_FORMAT(t_purchases.daily_report_date, '%Y/%m/%d') AS daily_report_date_formatted"),
                /*'t_purchases.departure_point_name',
                't_purchases.landing_name',
                't_purchases.total_fee',
                't_purchases.consumption_tax',
                't_purchases.tax_included_amount'*/
                't_purchases.*'
            )->where('mst_suppliers_cd',$mst_suppliers_cd)
            ->where('mst_business_office_id',$mst_business_office_id)
            ->where('invoicing_flag',0)
            ->where('daily_report_date','<=',$daily_report_date)
            ->whereNull('deleted_at')
            ->orderBy('daily_report_date');
        return $query->get();
    }
    public function updateInvoicingFlag($mst_suppliers_cd, $mst_business_office_id,$daily_report_date){
        $this::where(['mst_suppliers_cd'=>$mst_suppliers_cd,'mst_business_office_id'=>$mst_business_office_id])
            ->where('daily_report_date','<=',$daily_report_date)
            ->update([
                'invoicing_flag'=>1,
                'upd_mst_staff_id'=>Auth::user()->id,
                'modified_at'=>date("Y-m-d H:i:s",time()),
            ]);
    }
}