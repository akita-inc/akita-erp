<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class WriteLogs{
    public  $currentDate=null;
    public static function initLog($name)
    {
        $currentDate=date("[Y/m/d H:i:s]",time());
        $file =config('params.write_log.data_convert');
        $note= " データコンバート開始（".$name."）\n";
        file_put_contents($file, $currentDate.$note,FILE_APPEND);
    }
    public static function requireLogs($name,$filename,$field,$total)
    {
        $logDate = date("YmdHis");
        $currentDate=date("[Y/m/d H:i:s]",time());
        $file ="DataConvert_Err_required_".$name."_".$logDate.".log";
        $note= "▼ 必須エラー：".$filename."  ".$field."  ".$total."行目\n";
        file_put_contents($file, $currentDate.$note,FILE_APPEND);
    }
    public static function endLog($name,$total,$normal,$errors)
    {
        $currentDate=date("[Y/m/d H:i:s]",time());
        $file =config('params.write_log.data_convert');
        $note= " データコンバート終了（".$name."）
　　　　　　　　　　　データ件数：".$total."件". "  成功件数： ".$normal."件"."  失敗件数：".$errors."件\n";
        file_put_contents($file, $currentDate.$note,FILE_APPEND);
    }
}