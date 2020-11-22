<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolderHasModule extends Model
{
    protected $table = 'folder_has_module';
    protected $fillable = [
        'folder_id', 'module_id', 'created_at', 'updated_at'
    ];
    public $timestamps = false;
    public $incrementing = false;
}
