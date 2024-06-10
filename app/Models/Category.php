<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, TenantTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Belongs To Parent Category
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * Category has many category
     *
     * @return HasMany
     */
    public function categories()
    {
        return $this->hasMany(self::class);
    }
}
