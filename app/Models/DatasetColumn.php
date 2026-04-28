<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatasetColumn extends Model
{
    protected $fillable = ['dataset_id', 'name', 'data_type'];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }
}
