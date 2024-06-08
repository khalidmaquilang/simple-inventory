<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;

class CompaniesPermission
{
    /**
     * @param  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (! empty(auth()->user())) {
            session(['company_id' => Filament::getTenant()->id]);
            setPermissionsTeamId(Filament::getTenant()->id);
        }

        return $next($request);
    }
}
