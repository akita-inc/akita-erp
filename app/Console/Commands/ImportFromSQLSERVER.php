<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/24/2019
 * Time: 11:13 AM
 */
namespace App\Console\Commands;
use App\Console\Commands\ImportExcel\MstBusinessOffices;
use App\Models\MCustomers;
use App\Models\MPurchases;
use App\Models\MSaleses;
use App\Models\MStaffs;
use App\Models\MTJiconaxSalesDatas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class ImportFromSQLSERVER extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ImportFromSQLSERVER';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $dateTimeRun = '';
    protected $description = '';
    protected $connect = null;
    protected $countRead = 0;
    protected $countSuccess = 0;
    protected $countFails = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        date_default_timezone_set("Asia/Tokyo");
        $this->dateTimeRun = date("Ymd");
        $serverName = env("DB_SQLSERVER_HOST","172.30.30.193");
        $connectionInfo = [
            "Database" => env("DB_SQLSERVER_DB","AKITA"),
            "Uid" => env("DB_SQLSERVER_UID","sa"),
            "PWD" => env("DB_SQLSERVER_PWD","Shinway@123"),
            "CharacterSet" => "UTF-8"
        ];
        $this->connect = sqlsrv_connect( $serverName, $connectionInfo);
        if( $this->connect )
        {
            $this->log("jiconax","#################START####################");
            $this->insertTJiconaxDataSales();
        }
        else
        {
            echo "Connection could not be established.\n";
            die( print_r( sqlsrv_errors(), true));
        }

        $this->log("jiconax","#################END####################");
        $this->log("jiconaxFinal","ステータス：成功　処理対象件数：".$this->countRead."件　成功件数：".$this->countSuccess."件　エラー件数：".$this->countFails."件");
        sqlsrv_close( $this->connect );
    }

    protected function insertTJiconaxDataSales(){
        $getDateMax = MTJiconaxSalesDatas::query()->select(DB::raw("MAX(last_updated) as date"))->first();
        $sql = "SELECT * FROM M_運転日報_copy where [最終更新日] > CONVERT(datetime, '".$getDateMax["date"]."') OR　[最終更新日]　IS　NULL";
        $stmt = sqlsrv_query( $this->connect, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $arrayMapSalesData = [
            "daily_report_date" => '日報日付',
            "branch_office_cd" => '支店CD',
            "document_no" => '伝票NO',
            "operating_days" => '運転日数',
            "transport_cd" => '便CD',
            "vehicle_cd" => '車両CD',
            "point_of_departure" => '出発地',
            "destination" => '到着地',
            "staff_cd" => '社員CD',
            "mst_customers_cd" => '得意先CD',
            "goods" => '品物',
            "quantity" => '数量',
            "unit_price" => '単価',
            "transport_billing_amount" => '便請求金額',
            "insurance_fee" => '保険料',
            "billing_fast_charge" => '請求高速料',
            "discount_amount" => '値引金額',
            "billing_amount" => '請求金額',
            "consumption_tax" => '消費税額',
            "total_fee" => '合計金額',
            "departure_point" => '発地',
            "departure_point_name" => '発地名',
            "landing" => '着地',
            "landing_name" => '着地名',
            "delivery_destination" => '納入先',
            "payment" => '支払金額',
            "subcontract_amount" => '下請金額',
            "summary_indicator" => '集計区分',
            "actual_days" => '実日数',
            "mileage" => '走行距離',
            "actual_distance" => '実車距離',
            "refueling_fuel" => '補給燃料',
            "fast_charge" => '高速料金',
            "shipping_quantity" => '発送個数',
            "loading_rate" => '積載率',
            "round_trip_classification" => '往復区分',
            "tax_classification" => '課税区分',
            "discount_classification" => '値引区分',
            "opening_time" => '始業時間',
            "closing_time" => '終業時間',
            "restraint_time" => '拘束時間',
            "lunch_break_time" => '昼休憩時間',
            "night_break_time" => '夜休憩時間',
            "midnight_time" => '深夜時間',
            "working_time" => '労働時間',
            "lunch_break_time2" => '昼休息時間',
            "night_break_time2" => '夜休息時間',
            "predetermined_time" => '所定内時間',
            "off_hours" => '時間外',
            "custom_object_id" => 'ｶｽﾀﾑｵﾌﾞｼﾞｪｸﾄID',
            "last_updated_user" => '最終更新者',
            "last_updated" => '最終更新日',
            "loading_fee" => '積込料',
            "wholesale_fee" => '取卸料',
            "waiting_fee" => '待機料',
            "incidental_fee" => '附帯料',
            "surcharge_fee" => 'サーチャージ料',
        ];
        $arrayMapTSales = [
            "document_no" => '伝票NO',
            "branch_office_cd" => '支店CD',
            "last_updated" => '最終更新日',
            "last_updated_user" => '最終更新者',
            "daily_report_date" => '日報日付',
            "transport_cd" => '便CD',
            "staff_cd" => '社員CD',
            "vehicles_cd" => '車両CD',
            "goods" => '品物',
            "departure_point_name" => '発地名',
            "landing_name" => '着地名',
            "delivery_destination" => '納入先',
            "insurance_fee" => '保険料',
            "billing_fast_charge" => '請求高速料',
            "loading_fee" => '積込料',
            "wholesale_fee" => '取卸料',
            "waiting_fee" => '待機料',
            "incidental_fee" => '附帯料',
            "surcharge_fee" => 'サーチャージ料',
            "quantity" => '数量',
            "unit_price" => '単価',
            "discount_amount" => '値引金額',
            "summary_indicator" => '集計区分',
            "tax_classification_flg" => '課税区分',
            "payment" => '支払金額',
        ];
        $listBusiness = DB::table("mst_business_offices")->whereNull("deleted_at")->get()->pluck("id","branch_office_cd");
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $this->countRead++;
            DB::beginTransaction();
            try{
                $mTJiconaxSalesDatas = MTJiconaxSalesDatas::query()
                    ->where("document_no","=",$row["伝票NO"])
                ->first();
                if(empty($mTJiconaxSalesDatas)){
                    $mTJiconaxSalesDatas = new MTJiconaxSalesDatas();
                }

                $mCustomer = DB::table("mst_customers")->whereNull("mst_customers.deleted_at")->select("mst_customers.*","mst_account_titles.account_title_code")
                    ->leftJoin("mst_account_titles","mst_customers.mst_account_titles_id","mst_account_titles.id")
                    ->where("mst_customers_cd","=",$row["得意先CD"])
                    ->first();

                $mSuppliers = DB::table("mst_suppliers")->whereNull("mst_suppliers.deleted_at")->select("mst_suppliers.*","mst_account_titles.account_title_code")
                    ->leftJoin("mst_account_titles","mst_suppliers.mst_account_titles_id","mst_account_titles.id")
                    ->where("mst_suppliers_cd","=",$row["社員CD"])
                    ->first();

                foreach ( $arrayMapSalesData as $key=>$field ){
                    $mTJiconaxSalesDatas->$key = $row[$field];
                }

                if(!$mTJiconaxSalesDatas->save()){
                    $this->countFails++;
                    DB::rollBack();
                }else{
                    $flagError = false;
                    if( empty($row["労働時間"]) && isset($listBusiness[$mSaleses->branch_office_cd]) ) {
                        $mSaleses = MSaleses::query()
                            ->where("document_no", "=", $row["伝票NO"])
                            ->first();
                        if (empty($mSaleses)) {
                            $mSaleses = new MSaleses();
                        }

                        $mPurchases = MPurchases::query()
                            ->where("document_no", "=", $row["伝票NO"])
                            ->first();
                        if (empty($mPurchases)) {
                            $mPurchases = new MPurchases();
                        }

                        foreach ($arrayMapTSales as $key => $field) {
                            $mSaleses->$key = $row[$field];
                            $mPurchases->$key = $row[$field];
                        }

                        $mSaleses->mst_customers_cd = $row["得意先CD"];
                        $mPurchases->mst_suppliers_cd = $row["社員CD"];

                        if (isset($listBusiness[$mSaleses->branch_office_cd])) {
                            $mSaleses->mst_business_office_id = $listBusiness[$mSaleses->branch_office_cd];
                            $mPurchases->mst_business_office_id = $listBusiness[$mSaleses->branch_office_cd];
                        }

                        $mSaleses->invoicing_flag = 0;
                        $mPurchases->invoicing_flag = 0;
                        $mSaleses->total_fee = (($mSaleses->quantity * $mSaleses->unit_price) + $mSaleses->insurance_fee
                                + $mSaleses->loading_fee + $mSaleses->wholesale_fee + $mSaleses->waiting_fee
                                + $mSaleses->incidental_fee + $mSaleses->surcharge_fee - $mSaleses->discount_amount) + $mSaleses->billing_fast_charge;
                        $mPurchases->total_fee = $mPurchases->payment;
                        if ($mSaleses->tax_classification_flg == 2) {
                            $mSaleses->consumption_tax = 0;
                            $mPurchases->consumption_tax = 0;
                        } else {
                            $getTax = DB::table("consumption_taxs")
                                ->whereRaw("DATE_FORMAT('" . $mSaleses->daily_report_date->format('Y-m-d') . "',\"%Y/%m/%d\")  BETWEEN  DATE_FORMAT(start_date,\"%Y/%m/%d\") and 
DATE_FORMAT(end_date,\"%Y/%m/%d\")")->first();
                            if ($getTax) {
                                $mSaleses->consumption_tax = (($mSaleses->quantity * $mSaleses->unit_price) + $mSaleses->insurance_fee
                                        + $mSaleses->loading_fee + $mSaleses->wholesale_fee + $mSaleses->waiting_fee
                                        + $mSaleses->incidental_fee + $mSaleses->surcharge_fee - $mSaleses->discount_amount) * $getTax->rate;
                                if ($mCustomer) {
                                    switch ($mCustomer->rounding_method_id) {
                                        case 1:
                                            $mSaleses->consumption_tax = floor($mSaleses->consumption_tax);
                                            break;
                                        case 2:
                                            $mSaleses->consumption_tax = round($mSaleses->consumption_tax);
                                            break;
                                        case 3:
                                            $mSaleses->consumption_tax = ceil($mSaleses->consumption_tax);
                                            break;
                                        default:
                                            $mSaleses->consumption_tax = round($mSaleses->consumption_tax);
                                            break;
                                    }
                                } else {
                                    $mSaleses->consumption_tax = round($mSaleses->consumption_tax);
                                }

                                $mPurchases->consumption_tax = $mPurchases->total_fee * $getTax->rate;
                                if ($mSuppliers) {
                                    switch ($mSuppliers->rounding_method_id) {
                                        case 1:
                                            $mPurchases->consumption_tax = floor($mPurchases->consumption_tax);
                                            break;
                                        case 2:
                                            $mPurchases->consumption_tax = round($mPurchases->consumption_tax);
                                            break;
                                        case 3:
                                            $mPurchases->consumption_tax = ceil($mPurchases->consumption_tax);
                                            break;
                                        default:
                                            $mPurchases->consumption_tax = round($mPurchases->consumption_tax);
                                            break;
                                    }
                                } else {
                                    $mPurchases->consumption_tax = round($mPurchases->consumption_tax);
                                }
                            } else {
                                $mSaleses->consumption_tax = 0;
                                $mPurchases->consumption_tax = 0;
                            }
                        }

                        $mSaleses->tax_included_amount = $mSaleses->total_fee + $mSaleses->consumption_tax;
                        $mPurchases->tax_included_amount = $mPurchases->total_fee + $mPurchases->consumption_tax;
                        if ($mCustomer) {
                            $mSaleses->account_title_code = $mCustomer->account_title_code;
                        }
                        if ($mSuppliers) {
                            $mPurchases->account_title_code = $mSuppliers->account_title_code;
                        }
                        $mSaleses->add_mst_staff_id = 9999;
                        $mSaleses->upd_mst_staff_id = 9999;

                        $mPurchases->add_mst_staff_id = 9999;
                        $mPurchases->upd_mst_staff_id = 9999;
                        if (!$mSaleses->save()) {
                            $flagError = true;
                        }
                        if ( $mSuppliers ) {
                            if (!$mPurchases->save()) {
                                $flagError = true;
                            }
                        }
                    }
                    if( $flagError ){
                        $this->countFails++;
                        DB::rollBack();
                    }else {
                        $this->countSuccess++;
                        DB::commit();
                    }
                }

            }catch (\Exception $ex){
                $this->countFails++;
                DB::rollBack();
                $this->log("jiconax_error","伝票NO：".$mTJiconaxSalesDatas->document_no."　エラー理由：".$ex->getMessage());
            }
        }
    }

    protected function log($type,$message){
        switch ($type){
            case "jiconax":
            case "jiconaxFinal":
                $path = storage_path('logs/jiconax_input_'.$this->dateTimeRun.".log");
                break;
            case "jiconax_error":
                $path = storage_path('logs/jiconax_error_'.$this->dateTimeRun.".log");
                break;
        }

        $contentLog = "";
        if(file_exists($path)){
            $contentLog = file_get_contents( $path );
        }
        date_default_timezone_set("Asia/Tokyo");
        if($type == "jiconaxFinal"){
            $contentLog .= mb_convert_encoding($message, "SJIS")."\r\n";
        }else{
            $contentLog .= date("Y-m-d H:i:s ").mb_convert_encoding($message, "SJIS")."\r\n";
        }
        file_put_contents($path,$contentLog);
    }
}
