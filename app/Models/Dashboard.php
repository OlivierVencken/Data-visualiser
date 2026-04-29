<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $fillable = ['user_id', 'dataset_id', 'name', 'description', 'layout_config'];

    protected $casts = [
        'layout_config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function visualizations()
    {
        return $this->hasMany(Visualization::class);
    }
}
