<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = ['user_id', 'name', 'source_filename', 'row_count', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rows()
    {
        return $this->hasMany(DatasetRow::class);
    }

    public function uploads()
    {
        return $this->hasMany(DatasetUpload::class);
    }

    public function columns()
    {
        return $this->hasMany(DatasetColumn::class);
    }

    public function visualizations()
    {
        return $this->hasMany(Visualization::class);
    }
}
