<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Regions;
use App\Models\Communes;
use App\Models\Customers;
use App\Models\User;
use App\Models\UserToken;
use App\Models\Logs;

class MainController extends Controller
{
    //Consulta la informacion de un customer
    public function getInfo(Request $request){
        try {

            //Se genera log de entrada
            Logs::newLog($request, 'I', null, null);
            $response = null;

            //se realiza la busqueda segun el tipo
            if($request->type == 'email'){
                $response = Customers::findByEmail($request->field);
            }
            if ($request->type == 'dni') {
                $response = Customers::findByDni($request->field);
            }

            if($response != null){

                //en caso de APP_DEBUG ser true genera log de salida
                if (env('APP_DEBUG')) {
                    Logs::newLog($request, 'O', 'success', null);
                }
                //Respuesta
                return response()->json([
                    'success' => true,
                    'data' => [
                        'name' => $response->name,
                        'last_name' => $response->last_name,
                        'address' => ($response->address != '' && $response->address != null) ? $response->address : null,
                        'region' => $response->region()->first()->description,
                        'commune' => $response->commune()->first()->description,
                    ]
                ]);
            }else{
                if (env('APP_DEBUG')) {
                    Logs::newLog($request, 'O', 'error', 'Customer no Existe');
                }
                //Error al no conseguir customer
                return response()->json([
                    'success' => false,
                    'msg' => 'Customer no Existe.'
                ]); 
            }
            
        } catch (QueryException $e) {
            //Error al haber una excepcion en DB
            if (env('APP_DEBUG')) {
                Logs::newLog($request, 'O', 'error', 'Se ha producido un Error al consultar la Informacion');
            }
            return response()->json([
                'success' => false,
                'msg' => 'Se ha producido un Error al consultar la Informacion.'
            ]);
        }
    }

    //Agregar nuevo customer
    public function addCustomer(Request $request){

        try {

            //Se genera Log de entrada
            Logs::newLog($request, 'I', null, null);

            $customer = new Customers();
            $customer->dni = $request->dni;
            $customer->id_reg = $request->region;
            $customer->id_com = $request->commune;
            $customer->email = $request->email;
            $customer->address = !empty($request->address) ? $request->address : null;
            $customer->name = $request->name;
            $customer->last_name = $request->last_name;
            $customer->date_reg = date('Y-m-d H:i:s');
            $customer->status = 'A';
            $customer->save(); 

            //en caso de ser true genera log de salida
            if (env('APP_DEBUG')) {
                Logs::newLog($request, 'O', 'success', null);
            }
            //respuesta
            return response()->json([
                'success' => true,
                'msg' => 'Se ha agregado el Customer Existosamente.'
            ]);

        } catch (QueryException $e) {
            //Excepcion a nivel de DB
            if (env('APP_DEBUG')) {
                Logs::newLog($request, 'O', 'error', 'Se ha producido un Error al intentar agregar un Customer');
            }

            return response()->json([
                'success' => false,
                'msg' => 'Se ha producido un Error al intentar agregar un Customer.'
            ]);
        }
    }

    //Eliminar Customer
    public function deleteCustomer(Request $request){
        try {
            //Genera log de entrada
            Logs::newLog($request, 'I', null, null);
            $response = Customers::where('dni', $request->dni)->update(['status' => 'trash']);

            //Verifica que se haya eliminado correctamente
            if($response != null){

                //Se genera log de salida en caso de ser true
                if (env('APP_DEBUG')) {
                    Logs::newLog($request, 'O', 'success', null);
                }
                //respuesta
                return response()->json([
                    'success' => true
                ]);

            }else{

                if (env('APP_DEBUG')) {
                    Logs::newLog($request, 'O', 'error', 'Se ha producido un Error al intentar eliminar un Customer (No existe)');
                }
                //Error en caso de no haberse eliminado
                return response()->json([
                    'success' => false,
                    'msg' => 'Registro no existe.'
                ]);
            }
        } catch (QueryException $e) {

            //Error a nivel de DB
            if (env('APP_DEBUG')) {
                Logs::newLog($request, 'O', 'error', 'Se ha producido un Error al intentar eliminar un Customer');
            }

            return response()->json([
                'success' => false,
                'msg' => 'Se ha producido un Error al intentar eliminar un Customer.'
            ]);
        }
    }

    public function auth(Request $request){

        try {

            //Genera log de entrada
            Logs::newLog($request, 'I', null, null);
            //se busca el usuario
            $user = User::where('email', $request->email)->where('status', 'A')->first();

            if($user != null){
                //se valida la contraseña 
                if(Hash::check($request->password, $user->password)){

                    //se genera el token
                    $newtoken = sha1($user->email.(String)date('Y-m-d H:i:s').rand(200, 500));

                    $oldtoken = UserToken::where('user', $user->id);
                    //Verifica si hay token del mismo usuario
                    if($oldtoken->first() != null){
                        //lo actualiza
                        $oldtoken->update(['status' => 'A', 'token' => $newtoken, 'date_reg' => date("Y-m-d H:i:s"), 'ip' => $request->ip()]);
                    }else{
                        //lo crea
                        $token = new UserToken();
                        $token->user = $user->id;
                        $token->token = $newtoken;
                        $token->ip = $request->ip();
                        $token->date_reg = date('Y-m-d H:i:s');
                        $token->lifetime = 30;
                        $token->status = 'A';
                        $token->save();
                    }
                    //genera log de salida
                    if (env('APP_DEBUG')) {
                        Logs::newLog($request, 'O', 'success', null);
                    }

                    //respuesta
                    return response()->json(['success' => true, 'token' => $newtoken, 'lifetime' => '30 Minutes']);
                }
            }else{
                //Error al no existir usuario por correo
                if (env('APP_DEBUG')) {
                    Logs::newLog($request, 'O', 'error', 'Los Datos son Incorrectos');
                }

                return response()->json(['success' => false, 'msg' => 'Los Datos son Incorrectos.']);

            }
        } catch (QueryException $e) {

            //Error por DB
            if (env('APP_DEBUG')) {
                Logs::newLog($request, 'O', 'error', 'Se ha producido un Error al Autenticar');
            }

            return response()->json([
                'success' => false,
                'msg' => 'Se ha producido un Error al Autenticar.'
            ]);
        }
    }
}
