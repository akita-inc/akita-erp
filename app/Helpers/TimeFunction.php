<?php

/**
 * Created by PhpStorm.
 * User: tinhnv
 * Date: 5/8/2017
 * Time: 2:25 PM
 */

namespace App\Helpers;
use DateTime;
use Carbon\Carbon;
class TimeFunction
{
    static function getTimestamp($format = 'Y/m/d H:i:s'){
        $dt = new DateTime();
        return $dt->format($format);
    }

    static function dateFormat($time = '', $type = null)
	{
		if($time == '' || $time == "null") return null;
		$dt = Carbon::parse($time);
		switch ($type) {
            case $type: return $dt->format($type);break;
			default: return $dt->format('Y/m/d');
		}

	}
	
	//null: YYYY/MM/DD HH:MM:SS
	static function datetimeFormat($time = '', $type=null)
	{
		if($time == '') return '';
		$dt = Carbon::parse($time);
		switch ($type) {
			case 'time-short': return $dt->format('H:i');
			default: return $dt->format('Y/m/d H:i:s');
		}
	}

	static function convertMinuteToTime($time){
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return $hours.'時'.$minutes.'分';
    }

    static function convertShortTimeToMinute($shortTime){
        $times = explode(':', $shortTime);
        $minute = intval($times[0]) * 60 + intval($times[1]);
        return $minute;
    }

    static function convertMinuteToShortTime($minute, $zero_flg = false){
        if ($zero_flg || $minute != 0) {
            $hours = intval($minute / 60);
            $times =  sprintf("%'.02d", $minute % 60);
            $stringtime = "" . $hours . ":" . $times;
            return $stringtime;
        } else {
            return '';
        }
    }
    static function subOneDay($date, $format='Y-m-d'){
        return Carbon::parse($date)->subDay()->format($format);
    }

    static function parseStringToTime($time, $format='H:i:s'){
        return  date($format, strtotime($time));
    }

    static function convertTime24To12($time, $format="g:i A"){
        return  date($format, strtotime($time));
    }


}