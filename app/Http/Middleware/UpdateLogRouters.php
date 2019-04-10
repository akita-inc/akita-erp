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
            $acceplog->ip_address = $this->getClientIp();
            $acceplog->save();
        }
        return $next($request);
    }
    protected  function getClientIp() {

        $result = null;

        $ipSourceList = array(
            'HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED', 'REMOTE_ADDR'
        );

        foreach($ipSourceList as $ipSource){
            if ( isset($_SERVER[$ipSource]) ){
                $result = $_SERVER[$ipSource];
                break;
            }
        }
        return $result;
    }
}
