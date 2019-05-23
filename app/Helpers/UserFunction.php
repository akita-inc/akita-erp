<?php

/**
 * Created by PhpStorm.
 * User: tinhnv
 * Date: 5/8/2017
 * Time: 2:26 PM
 */

namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
class UserFunction extends Auth
{
    static function getCurrentUserId(){
        return Auth::id();
    }

    static function getCurrentUserName(){
        return Auth::user()->user_name;
    }
    static function getCurrentUser(){
        return json_decode( json_encode(Auth::user()), true ) ;
    }
    static function getClientIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}