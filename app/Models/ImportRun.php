<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportRun extends Model
{
    protected $fillable = ['dataset_upload_id', 'status', 'rows_processed', 'error_message'];

    public function datasetUpload()
    {
        return $this->belongsTo(DatasetUpload::class);
    }
}
