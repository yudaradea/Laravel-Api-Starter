<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use UUID;

    protected $fillable = [
        'user_id',
        'avatar',
        'bio',
        'phone',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
