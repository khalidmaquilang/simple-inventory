<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

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
        return storage_path('app/public/'.self::first()->company_logo);
    }
}
