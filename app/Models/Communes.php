<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communes extends Model
{
    protected $table = 'communes';

    protected $fillable = [
        'description',
        'status'
    ];

    protected $primaryKey = 'id_com';

    public $timestamps = false;

    //Relacion con RegionS
    public function region()
    {
        return $this->hasOne(Regions::class, 'id', 'id_reg');
    }
}
