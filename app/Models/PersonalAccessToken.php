<?php

namespace App\Models;

use App\Traits\UUID;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use UUID;

    protected $table = 'personal_access_tokens';

    public $incrementing = false;

    protected $keyType = 'string';
}
