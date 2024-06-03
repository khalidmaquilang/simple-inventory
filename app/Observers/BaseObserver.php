<?php

namespace App\Observers;

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
        }
    }
}
