<?php

namespace App\Providers;

use App\Http\Middleware\SubscriptionMiddleware;
use Closure;
use Filament\Billing\Providers\Contracts\Provider;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class CustomBillingProvider implements Provider
{

    /**
     * @inheritDoc
     */
    public function getRouteAction(): Closure
    {
        return function (): RedirectResponse {
            return redirect(route('filament.app.pages.custom-billing', filament()->getTenant()));
        };
    }

    public function getSubscribedMiddleware(): string
    {
        return SubscriptionMiddleware::class;
    }
}
