<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatasetUpload extends Model
{
    protected $fillable = ['dataset_id', 'original_filename', 'storage_path', 'status'];

    public function dataset()
    {
        return $this->belongsTo(Dataset::class);
    }

    public function importRuns()
    {
        return $this->hasMany(ImportRun::class);
    }
}
