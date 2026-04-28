<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatasetRow extends Model
{
    protected $fillable = ['dataset_id', 'row_index', 'data'];

    protected $casts = [
        'data' => 'array'
    ];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}
