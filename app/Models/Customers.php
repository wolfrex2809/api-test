<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $table = 'customers';

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

    //Relacion con Regions
    public function region()
    {
        return $this->hasOne(Regions::class, 'id_reg', 'id_reg');
    }
    //Relacion con Communes
    public function commune()
    {
        return $this->hasOne(Communes::class, 'id_com', 'id_com');
    }

    //Buscar Customer por Email
    public static function findByEmail($email){
        return self::select('name', 'last_name', 'address', 'id_reg', 'id_com')->where('email', $email)->where('status', 'A')->first();
    }
    //Buscar Customer por Dni
    public static function findByDni($dni){
        return self::select('name', 'last_name', 'address', 'id_reg', 'id_com')->where('dni', $dni)->where('status', 'A')->first();
    }

}
