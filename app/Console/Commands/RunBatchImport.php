<?php
/**
 * Created by PhpStorm.
 * User: sonpt
 * Date: 4/24/2019
 * Time: 11:13 AM
 */
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunBatchImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $arrayRunTime = ['mst_staff_dependents'];
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
        foreach ($this->arrayRunTime as $run){
            echo Artisan::call("ConvertDataByExcels", ['--type' => $run]);
        }

    }
}
