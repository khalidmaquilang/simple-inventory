<?php

namespace App\Models;

use App\Models\Traits\TenantTrait;

class Role extends \Spatie\Permission\Models\Role
{
    use TenantTrait;
}
