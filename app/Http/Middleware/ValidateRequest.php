<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Models\Regions;
use App\Models\Communes;

class ValidateRequest
{
    public function handle($request, Closure $next){

        try {
            //Se consulta cual es la ruta usada y se ejecuta una validacion para cada una.
            switch ($request->path()) {

                case 'auth':
                    $request->validate([
                        'email' => 'required | email',
                        'password' => 'required'
                    ]);
                    break;

                case 'getCustomers':
                    $request->validate([
                        'type' => ['required', Rule::in(['dni', 'email'])],
                        'field' => 'required'
                    ]);
                    break;

                case 'addCustomers':
                    $request->validate([
                        'dni' => 'required',
                        'email' => 'required | email',
                        'name' => 'required',
                        'last_name' => 'required',
                        'region' => 'required | integer',
                        'commune' => 'required | integer'
                    ]);

                    $commune = Communes::where('id_com', $request->commune)->where('status', 'A')->first();
                    //Se validan la comuna y la region
                    if($commune != null){
                        if($commune->id_reg != $request->region){
                            return response()->json([
                                'success' => false,
                                'msg' => 'Faltan Datos | Region no existe.'
                            ], 400);
                        }
                    }else{
                        return response()->json([
                            'success' => false,
                            'msg' => 'Faltan Datos | Comuna no existe.'
                        ], 400);
                    }
                    break;

                case 'deleteCustomers':
                    $request->validate([
                        'dni' => 'required'
                    ]);
                    break;
                
                default:
                    //En caso de no ser ninguna de las anteriores.
                    return response()->json([
                        'success' => false,
                    ], 400);
                    break;
            }
            //Continua la ejecucion en caso de no haber saltado alguna excepcion
            return $next($request);

        } catch (ValidationException $e) {
            //Error en caso de una excepcion en la validacion
            return response()->json([
                'success' => false,
                'msg' => 'Datos Invalidos | Valide e intente de nuevo.'
            ], 400);

        }
        //Error en caso de continuar ejecucion (en caso de crear una ruta y no validarla por este medio).
        return response()->json([
            'success' => false,
        ], 400);
    }
}