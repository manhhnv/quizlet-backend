<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'username', 'created_at', 'updated_at', 'user_id'
    ];
    public $incrementing = false;
    public $timestamps = false;
}
