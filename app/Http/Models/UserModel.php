<?php

namespace MiSAKACHi\VERACiTY\Http\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable {
    use Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public $table      = 'vrc_users';
    public $primaryKey = 'emailAddress';
    public $timestamps = false;
}


