<?php

namespace App\Http\Middleware;

use App\Jobs\ProcessRequestLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class RequestLog
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = '127.0.0.1';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = $ips[0];
        }

        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }
        ProcessRequestLog::dispatch($ip, $request->method(), $request->getRequestUri(), Carbon::now());
        return $next($request);
    }
}
