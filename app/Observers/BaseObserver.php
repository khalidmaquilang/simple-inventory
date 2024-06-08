<?php

namespace App\Observers;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class BaseObserver
{
    /**
     * Handle the Model "creating" event.
     */
    public function creating(Model $model): void
    {
        if (auth()->check()) {
            $model->user_id = auth()->user()->id;
            $model->company_id = Filament::getTenant()->id;
        }
    }
}
