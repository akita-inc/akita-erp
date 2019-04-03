<?php

namespace App\Console\Commands;

use App\Models\MEmptyInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateStatusEmptyInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateStatusEmptyInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
下記の仕様で１日１度実行されるバッチを作成してください。

＜対象データ＞

・ステータス（empty_info.status）が「1：受付中」「2：仮押さえ」
・到着日（empty_info.arrive_date）が今日以前のデータすべて

＜更新内容＞

・ステータスを「9：アンマッチ」に変更
・更新日付を「バッチ実行時間」に変更
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
        MEmptyInfo::where(function ($query){
            $query->where("status","=","1");
            $query->orWhere("status","=","2");
        })->where(DB::raw("DATE_FORMAT(arrive_date, '%Y/%m/%d')"),"<",date("Y/m/d",time()))
            ->update(["status"=>"9","modified_at"=>date("Y-m-d H:i:s",time())]);
    }
}
