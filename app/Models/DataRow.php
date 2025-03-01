<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRow extends Model
{
    protected $fillable = [
        'data_type_id', 'field', 'type', 'display_name', 'required',
        'browse', 'read', 'edit', 'add', 'delete', 'details', 'order'
    ];

    public function dataType()
    {
        return $this->belongsTo(DataType::class);
    }
}
