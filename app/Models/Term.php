<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'term';
    protected $fillable = [
        'id', 'question', 'explain', 'score', 'module_id',
        'created_at', 'updated_at'
    ];
    public $timestamps = false;
    public function module() {
        return $this->belongsTo('App\Models\Module', 'module_id', 'id');
    }
}
