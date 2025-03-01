<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataType extends Model
{
    protected $fillable = ['name', 'slug', 'model_name'];

    public function rows()
    {
        return $this->hasMany(DataRow::class);
    }
}
