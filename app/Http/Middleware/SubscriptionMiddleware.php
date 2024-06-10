<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;

class SubscriptionMiddleware
{
    /**
     * @param  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
