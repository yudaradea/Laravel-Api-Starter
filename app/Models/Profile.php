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

    protected $appends = ['avatar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
}
