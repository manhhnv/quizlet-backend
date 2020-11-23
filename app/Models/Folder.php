<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table = 'folder';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'name', 'public', 'user_id', 'description',
        'created_at', 'updated_at', 'code'
    ];
    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function modules() {
        return $this->hasMany('App\Models\FolderHasModule', 'folder_id', 'id');
    }
}
