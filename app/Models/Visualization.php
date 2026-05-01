<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visualization extends Model
{
    protected $fillable = ['user_id', 'dataset_id', 'dashboard_id', 'name', 'type', 'config', 'position'];

    protected $casts = [
        'config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class);
    }
}
