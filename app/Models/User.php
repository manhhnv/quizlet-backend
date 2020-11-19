<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $guarded = 'users';
    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $fillable = [
        'id', 'username', 'email', 'avatar', 'remember_token',
        'created_at', 'updated_at', 'verified', 'password', 'verify_code'
    ];
    protected $hidden = ['password', 'remember_token', 'verify_code'];
    public $timestamps = false;
}
