<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;    protected $table = 'regions';

    protected $fillable = [
        'email',
        'name',
        'last_name',
        'address',
        'date_reg',
        'status',
    ];

    protected $primaryKey = 'dni';

    public $timestamps = false;

}
