<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/24/2019
 * Time: 11:13 AM
 */
namespace App\Console\Commands;
use App\Models\MStaffs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RunBatchImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $arrayRunTime = [
        'mst_business_offices',
        'mst_staffs',
        'mst_staff_dependents',
        'mst_staff_qualifications',
        'mst_vehicles',
        'mst_customers',
        'mst_suppliers',
        'mst_bill_issue_destinations',
    ];
    protected $signature = 'RunBatchImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
コンバート用のバッチを一括で処理するバッチを作成してください。
【処理概要】
1.テーブルのTrancate
2.汎用マスタの登録
3.営業所マスタ作成バッチ実行
4.社員マスタ作成バッチ実行
5.社員扶養者マスタ作成バッチ実行
6.社員扶養者マスタ作成バッチ実行
7.社員保有資格マスタ作成バッチ実行
8.車両マスタ作成バッチ実行
9.得意先マスタ作成バッチ実行
10.仕入先マスタ作成バッチ実行
11.請求書発行先住所マスタ作成バッチ実行
';

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
        ini_set('memory_limit', -1);
        $files = glob(storage_path("logs")."/DataConvert_Add_general_purposes_*"); // Will find 2.txt, 2.php, 2.gif

        if (count($files) > 0){
            foreach ($files as $file)
            {
                try{
                    $myfile = fopen($file, "r") or die("Unable to open file!");
                    while(!feof($myfile)) {
                        $contentLine = fgets($myfile);
                        $strSplit = explode(mb_convert_encoding("データ区分：", "SJIS"),$contentLine);
                        if(count($strSplit) > 1){
                            $strSplit = explode(mb_convert_encoding("データ区分名：", "SJIS"),$strSplit[1]);
                            $data_kb = trim(trim($strSplit[0]),mb_convert_encoding("　", "SJIS"));

                            $strSplit = explode(mb_convert_encoding("データID：", "SJIS"),$strSplit[1]);
                            $data_kb_nm = trim(trim($strSplit[0]),mb_convert_encoding("　", "SJIS"));

                            $strSplit = explode(mb_convert_encoding("データ名称：", "SJIS"),$strSplit[1]);
                            $data_id = trim(trim($strSplit[0]),mb_convert_encoding("　", "SJIS"));
                            $strSplit = explode(mb_convert_encoding("データカナ名称：", "SJIS"),$strSplit[1]);
                            $data_nm = trim(trim($strSplit[0]),mb_convert_encoding("　", "SJIS"));
                            $mst_general_purposes = DB::table("mst_general_purposes")
                                ->where("data_kb","=",$data_kb)
                                ->where("date_nm","=",$data_nm)
                                ->where("date_id","=",$data_id)
                                ->first();
                            if(empty($mst_general_purposes)){
                                DB::table("mst_general_purposes")->insert([
                                    "data_kb" => $data_kb,
                                    "data_kb_nm" => mb_convert_encoding($data_kb_nm, "UTF-8", "SJIS"),
                                    "date_id" => $data_id,
                                    "date_nm" => mb_convert_encoding($data_nm, "UTF-8", "SJIS"),
                                    "date_nm_kana" => "フメイ"
                                ]);
                            }
                        }

                    }
                    fclose($myfile);
                }catch (\Exception $ex){
                    echo $ex->getMessage();
                }
            }
        }
        foreach ($this->arrayRunTime as $run){
            if($run == "mst_staffs"){
                $staffAdmin = DB::table($run)->where("staff_cd","=","admin")->first();
                if( $staffAdmin ){
                    $staffAdmin = (array)$staffAdmin;
                    unset($staffAdmin["id"]);
                }
            }
            DB::table($run)->truncate();

            if($run == "mst_staffs"){
                if( $staffAdmin ) {
                    DB::table($run)->insert($staffAdmin);
                }
            }
        }
        foreach ($this->arrayRunTime as $run){
            Artisan::call("ConvertDataByExcels", ['--type' => $run]);
        }

    }
}
