<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'module';
    protected $fillable = [
        'id', 'name', 'max_score', 'user_id', 'class_id', 'folder_id',
        'public', 'created_at', 'updated_at', 'description'
    ];

    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function terms() {
        return $this->hasMany('App\Models\Term', 'module_id', 'id');
    }
}
