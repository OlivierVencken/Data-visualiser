<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserColorTheme extends Model
{
    protected $fillable = ['user_id', 'name', 'colors'];

    protected $casts = [
        'colors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
