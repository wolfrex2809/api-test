<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'description',
        'email',
        'password',
        'date_reg',
        'status',
    ];

    public $timestamps = false;
}
