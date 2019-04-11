<?php

namespace App\Http\Middleware;

use App\Models\MAccessLogs;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

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
        if(Auth::check()) {
            $acceplog->mst_staff_id = Auth::user()->id;
        }else{
            $acceplog->mst_staff_id = Null;
        }
        $acceplog->http_user_agent = $_SERVER["HTTP_USER_AGENT"];
        $acceplog->ip_address = $this->getClientIp();
        $acceplog->save();
        return $next($request);
    }

    // 指定されたサーバー環境変数を取得する
    protected function getServer($key, $default = null)
    {
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    // クライアントのIPアドレスを取得する
    protected function getClientIp($checkProxy = true)
    {
        /*
         *  プロキシサーバ経由の場合は、プロキシサーバではなく
         *  接続もとのIPアドレスを取得するために、サーバ変数
         *  HTTP_CLIENT_IP および HTTP_X_FORWARDED_FOR を取得する。
         */
        if ($checkProxy && $this->getServer('HTTP_CLIENT_IP') != null) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else if ($checkProxy && $this->getServer('HTTP_X_FORWARDED_FOR') != null) {
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            // プロキシサーバ経由でない場合は、REMOTE_ADDR から取得する
            $ip = $this->getServer('REMOTE_ADDR');
        }
        return $ip;
    }
}
