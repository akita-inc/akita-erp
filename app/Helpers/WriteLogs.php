<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class WriteLogs{
    public  $currentDate=null;
    public static function initLog()
    {
        $currentDate=date("[Y/m/d H:i:s]",time());
        $file =config('params.import_file_path.data_convert');
        $note= " データコンバート開始（社員）";
        file_put_contents($file, $currentDate.$note);
    }
    public static function endLog($total,$normal,$errors)
    {
        $currentDate=date("[Y/m/d H:i:s]",time());
        $file =config('params.import_file_path.data_convert');
        $note= " データコンバート終了（社員）
　　　　　　　　　　　　データ件数：".$total."件". "  成功件数： ".$normal."件"."  失敗件数：".$errors."件"; //（số đọc dữ liệu）（số dữ liệu bình thường）　số dữ liệu có error）";
        file_put_contents($file, $currentDate.$note);
    }
}