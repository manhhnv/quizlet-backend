<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testing extends Model
{
    protected $table = 'testing';
    protected $fillable = [
        'answer', 'correct', 'term_id'
    ];
    public $incrementing = false;
    public $timestamps = false;
}
