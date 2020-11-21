<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassHasModule extends Model
{
    protected $table = "class_has_module";
    protected $fillable = [
        'module_id', 'class_id', 'created_at', 'updated_at'
    ];

    public $incrementing = false;
    public $timestamps = false;
}
