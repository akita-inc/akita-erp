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

class DeleteJICONAXFromSQLSERVER extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteJICONAXFromSQLSERVER';

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
        $this->log("jiconax","#################START####################");
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
            $arrayDocumentNo = [];
            $sql = "SELECT [伝票NO] FROM M_運転日報 WHERE CONVERT(datetime, [最終更新日]) > DATEADD(DAY, -60,getdate())";
            $stmt = sqlsrv_query( $this->connect, $sql );
            if( $stmt === false) {
                die( print_r( sqlsrv_errors(), true) );
            }
            while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                $arrayDocumentNo[$row["伝票NO"]] = $row["伝票NO"];
            }
            $this->deleteJiconax($arrayDocumentNo);
            $this->deleteSalesORPurchases($arrayDocumentNo,"t_saleses");
            $this->deleteSalesORPurchases($arrayDocumentNo,"t_purchases");
        }
        else
        {
            echo "Connection could not be established.\n";
            return true;
        }

        $this->log("jiconax","#################END####################");
        $this->log("jiconaxFinal","ステータス：成功　処理対象件数：".$this->countRead."件　成功件数：".$this->countSuccess."件　エラー件数：".$this->countFails."件");
        sqlsrv_close( $this->connect );
        return true;
    }

    protected function deleteJiconax($arrayDocumentNo){
        $queryWhere = 'DATE_FORMAT(last_updated,"%Y/%m/%d") > DATE_FORMAT(DATE_SUB(NOW(),INTERVAL '.config("params.runDeleteLogicFromSqlServer.day_minus").' DAY),"%Y/%m/%d")';
        $listJconaxModel = DB::table("t_jiconax_sales_datas")->select("document_no")
            ->whereNull("deleted_at")
            ->whereRaw($queryWhere)
            ->get();
        foreach ($listJconaxModel as $value){
            $this->countRead++;
            if( !isset($arrayDocumentNo[$value->document_no]) ){
                try{
                    DB::table("t_jiconax_sales_datas")
                        ->where("document_no",$value->document_no)
                        ->update(["deleted_at"=>DB::raw("Now()")]);
                    $this->countSuccess++;
                }catch (\Exception $ex){
                    $this->countFails++;
                    $this->log("jiconax_error","伝票NO：".$value->document_no."　エラー理由：".$ex->getMessage());
                }
            }
        }
    }

    protected function deleteSalesORPurchases($arrayDocumentNo,$table){
        $queryWhere = 'DATE_FORMAT(created_at,"%Y/%m/%d") > DATE_FORMAT(DATE_SUB(NOW(),INTERVAL '.config("params.runDeleteLogicFromSqlServer.day_minus").' DAY),"%Y/%m/%d")';
        $listSales = DB::table($table)->select("document_no")
            ->whereNull("deleted_at")
            ->whereRaw($queryWhere)
            ->where("invoicing_flag","<>","1")
            ->get();

        foreach ($listSales as $value){
            $this->countRead++;
            if( !isset($arrayDocumentNo[$value->document_no]) ){
                try{
                    DB::table($table)
                        ->where("document_no",$value->document_no)
                        ->update(["deleted_at"=>DB::raw("Now()")]);
                    $this->countSuccess++;
                }catch (\Exception $ex){
                    $this->countFails++;
                    $this->log("jiconax_error","伝票NO：".$value->document_no."　エラー理由：".$ex->getMessage());
                }
            }
        }
    }

    protected function log($type,$message){
        switch ($type){
            case "jiconax":
            case "jiconaxFinal":
                $path = storage_path('logs/jiconax_delete_'.$this->dateTimeRun.".log");
                break;
            case "jiconax_error":
                $path = storage_path('logs/jiconax_delete_error_'.$this->dateTimeRun.".log");
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