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
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']
            === 'on' ? "https" : "http") . "://" .
            $_SERVER['HTTP_HOST'];
        $link .= $_SERVER['REQUEST_URI'];
        $acceplog = new MAccessLogs();
        $acceplog->url = $link;
        $acceplog->mst_staff_id = Auth::user()->id;
        $acceplog->http_user_agent = $_SERVER["HTTP_USER_AGENT"];
        $acceplog->ip_address = $_SERVER["REMOTE_ADDR"];
        $acceplog->save();
        return $next($request);
    }
}
