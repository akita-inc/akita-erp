<?php

namespace App\Http\Middleware;

use App\Models\MAccessLogs;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class UpdateLogRouters extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    public function handle($request, Closure $next, $guard = null)
    {
        if(Auth::check()) {
            $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
                === 'on' ? "https" : "http") . "://" .
                $_SERVER['HTTP_HOST'];
            $link .= $_SERVER['REQUEST_URI'];
            $acceplog = new MAccessLogs();
            $acceplog->url = $link;
            $acceplog->mst_staff_id = Auth::user()->id;
            $acceplog->http_user_agent = $_SERVER["HTTP_USER_AGENT"];
            //$acceplog->ip_address = $_SERVER["REMOTE_ADDR"];
            $acceplog->ip_address = $this->get_client_ip_server();
            $acceplog->save();
        }
        return $next($request);
    }
    protected  function get_client_ip_server() {
        $ipaddress = '';
        if (!isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(!isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(!isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(!isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(!isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
}
