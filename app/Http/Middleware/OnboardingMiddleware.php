<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnboardingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty(Filament::getTenant()?->id)) {
            return $next($request);
        }

        $setting = Setting::first();
        if (! empty($setting)) {
            return $next($request);
        }

        return redirect(route('filament.app.pages.settings', ['tenant' => Filament::getTenant()->id]));
    }
}
