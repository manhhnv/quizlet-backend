<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'member';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username', 'created_at', 'updated_at'
    ];
    public $timestamps = false;
}
