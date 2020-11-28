<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $primaryKey = 'id';

    protected $table = 'class';
    protected $fillable = [
        'id', 'name', 'public', 'user_id', 'created_at', 'updated_at', 'code',
        'description'
    ];
    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
