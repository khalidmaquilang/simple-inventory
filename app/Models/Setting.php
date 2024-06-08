<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, TenantTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('company', function (Builder $builder) {
            $builder->where('company_id', session('company_id'));
        });
    }

    /**
     * @return string
     */
    public static function getCurrency(): string
    {
        return self::first()->currency;
    }

    /**
     * @return string
     */
    public static function getCompanyLogo(): string
    {
        $logo = self::first()->company_logo;
        if (empty($logo)) {
            return '';
        }

        return storage_path('app/public/'.$logo);
    }
}
