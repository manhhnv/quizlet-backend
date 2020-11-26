<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'name'];
    public function permissions() {
        return $this->belongsToMany('App\Models\Permission');
    }
}
