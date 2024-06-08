<?php

namespace App\Models\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TenantTrait
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
