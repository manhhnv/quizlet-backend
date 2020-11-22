<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembersHasClasses extends Model
{
    protected $table = 'members_has_classes';
    protected $fillable = [
        'member_id', 'class_id', 'created_at', 'updated_at'
    ];
    public $timestamps = false;
    public $incrementing = false;
}
