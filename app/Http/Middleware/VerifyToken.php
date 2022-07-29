<?php

namespace App\Http\Middleware;
use Carbon\Carbon;
use App\Models\UserToken;
use Closure;

class VerifyToken
{
    public function handle($request, Closure $next){

        try {
                
            $msg = 'Error al Autenticar | Token Invalido';
            //Se verifica que el token venga en el request
            if(!empty($request->bearerToken())){

                //Se verifica que el token exista
                $token = UserToken::where('token', $request->bearerToken())->first();
                if($token != null){
                    //Se verifica que la ip almacenada coincida
                    if($token->ip == $request->ip()){

                        $reg = new Carbon($token->date_reg);
                        //Se verifica el timeout del token
                        if($reg->diffInMinutes(Carbon::now()) < $token->lifetime && $token->status == 'A'){

                            //Continua la ejecucion del request
                            return $next($request);
                        }else{

                            //Se cambia el estado del token
                            $token->update(['status' => 'trash']);
                            $msg = 'Error al Autenticar | Token Expirado.';
                        }
                    }
                }
            }
            //Mensaje de error en caso de no cumplir con las verificaciones previa 
            return response()->json([
                'success' => false,
                'msg' => $msg
            ], 401);
        } catch (QueryException $e) {
            //Mensaje de error en caso algun error con la DB 
            return response()->json([
                'success' => false,
                'msg' => $msg
            ], 401);
        }
    }
}