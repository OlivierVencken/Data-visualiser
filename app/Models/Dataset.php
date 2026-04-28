<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    protected $fillable = ['user_id', 'name', 'source_filename', 'row_count', 'status'];

    public function rows()
    {
        return $this->hasMany(DatasetRow::class);
    }
}
