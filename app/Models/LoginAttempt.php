<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    public $table = "loginattempts";

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'IP',
        'username',
        'usertype',
        'Attempts',
        'LastLogin',
        'sessionid',
        'loginsuccess',
        'logintype',
        'loginvia'
    ];

}
