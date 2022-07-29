<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_token';

    protected $fillable = [
        'token',
        'ip',
        'date_reg',
        'lifetime',
        'status',
    ];

    public $timestamps = false;

    //Relacion con User
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }
}