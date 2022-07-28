<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $table = 'regions';

    protected $fillable = [
        'description',
        'status'
    ];

    protected $primaryKey = 'id_reg';

    public $timestamps = false;
}
