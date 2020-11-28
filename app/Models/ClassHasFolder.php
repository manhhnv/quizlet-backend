<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassHasFolder extends Model
{
    protected $table = 'class_has_folder';
    protected $fillable = [
        'id',
        'folder_id', 'class_id', 'created_at', 'updated_at'
    ];
//    public $incrementing = false;
    public $timestamps = false;
}
