<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'type',
        'request',
        'data',
        'output',
        'comments',
        'ip',
        'date_reg',
    ];

    public $timestamps = false;

    //Relacion con User
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    //Crea un nuevo Log en DB
    public static function newLog($request, $type, $output = null, $comments = null){

        $user = UserToken::where('token', $request->bearerToken())->first();

        $log = new Logs();
        $log->type = $type;
        $log->request = $request->url();
        $log->data = (String)$request->getContent();
        $log->output = $output;
        $log->comments = $comments;
        $log->user = !empty($user->user)?$user->user:null;
        $log->ip = $request->ip();
        $log->date_reg = date('Y-m-d H:i:s');
        $log->save();

        return true;
    }
}